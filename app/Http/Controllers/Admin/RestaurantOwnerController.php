<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use App\Models\Modern\FoodOrder;
use App\Models\Modern\Restaurant;
use App\Models\Payout;
use App\Models\VendorWallet;
use Gate;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RestaurantOwnerController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('app_user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = AppUser::query()
            ->where('user_type', 'restaurant_owner')
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where(function ($search) use ($keyword) {
                $search
                    ->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        $owners = $query->paginate(25)->appends($request->query());
        $ownerIds = $owners->pluck('id')->map(fn ($id) => (int) $id)->values();

        $restaurants = Restaurant::query()
            ->withCount(['branches', 'items'])
            ->whereIn('owner_id', $ownerIds)
            ->get()
            ->keyBy('owner_id');

        $orderStats = FoodOrder::query()
            ->selectRaw('restaurant_id, COUNT(*) as orders_count, COALESCE(SUM(total_amount), 0) as total_sales')
            ->whereIn('restaurant_id', $restaurants->pluck('id')->filter()->values())
            ->groupBy('restaurant_id')
            ->get()
            ->keyBy('restaurant_id');

        $walletStats = VendorWallet::query()
            ->selectRaw("
                vendor_id,
                COALESCE(SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END), 0) as total_credit,
                COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END), 0) as total_debit
            ")
            ->whereIn('vendor_id', $ownerIds)
            ->groupBy('vendor_id')
            ->get()
            ->keyBy('vendor_id');

        $counts = [
            'total' => AppUser::where('user_type', 'restaurant_owner')->count(),
            'active' => AppUser::where('user_type', 'restaurant_owner')->where('status', 1)->count(),
            'inactive' => AppUser::where('user_type', 'restaurant_owner')->where('status', 0)->count(),
            'with_restaurant' => Restaurant::whereIn('owner_id', AppUser::where('user_type', 'restaurant_owner')->select('id'))->count(),
        ];

        return view('admin.restaurantOwners.index', compact('owners', 'restaurants', 'orderStats', 'walletStats', 'counts'));
    }

    public function show(AppUser $owner)
    {
        abort_if(Gate::denies('app_user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->ensureRestaurantOwner($owner);

        $restaurant = Restaurant::query()
            ->with(['branches', 'categories', 'items'])
            ->where('owner_id', $owner->id)
            ->first();

        $orders = collect();
        $orderSummary = [
            'total_orders' => 0,
            'active_orders' => 0,
            'delivered_orders' => 0,
            'cancelled_orders' => 0,
            'total_sales' => 0,
        ];

        if ($restaurant) {
            $orders = FoodOrder::query()
                ->with(['customer:id,first_name,last_name,email,phone,phone_country', 'driver:id,first_name,last_name,phone,phone_country'])
                ->where('restaurant_id', $restaurant->id)
                ->latest('id')
                ->limit(20)
                ->get();

            $orderSummary = FoodOrder::query()
                ->where('restaurant_id', $restaurant->id)
                ->selectRaw("
                    COUNT(*) as total_orders,
                    SUM(status IN ('placed', 'accepted', 'preparing', 'ready_for_pickup', 'picked_up', 'on_the_way')) as active_orders,
                    SUM(status = 'delivered') as delivered_orders,
                    SUM(status = 'cancelled') as cancelled_orders,
                    COALESCE(SUM(total_amount), 0) as total_sales
                ")
                ->first()
                ->toArray();
        }

        $wallet = $this->walletSummary($owner->id);
        $withdrawals = Payout::query()
            ->where('vendorid', $owner->id)
            ->where('module', 3)
            ->latest('id')
            ->limit(10)
            ->get();

        return view('admin.restaurantOwners.show', compact('owner', 'restaurant', 'orders', 'orderSummary', 'wallet', 'withdrawals'));
    }

    public function update(Request $request, AppUser $owner)
    {
        abort_if(Gate::denies('app_user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->ensureRestaurantOwner($owner);

        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'nullable|string|max:191',
            'email' => ['nullable', 'email', 'max:191', Rule::unique('app_users', 'email')->ignore($owner->id)],
            'phone' => ['nullable', 'string', 'max:50', Rule::unique('app_users', 'phone')->ignore($owner->id)],
            'phone_country' => 'nullable|string|max:20',
            'status' => 'required|in:0,1',
            'email_notification' => 'nullable|in:0,1',
            'sms_notification' => 'nullable|in:0,1',
            'push_notification' => 'nullable|in:0,1',
        ]);

        $data['user_type'] = 'restaurant_owner';
        $owner->update($data);

        return redirect()
            ->route('admin.restaurant-owners.show', $owner->id)
            ->with('success', 'Restaurant owner account updated successfully.');
    }

    public function updatePassword(Request $request, AppUser $owner)
    {
        abort_if(Gate::denies('app_user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->ensureRestaurantOwner($owner);

        $data = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $owner->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('admin.restaurant-owners.show', $owner->id)
            ->with('success', 'Restaurant owner password changed successfully.');
    }

    public function toggleStatus(AppUser $owner)
    {
        abort_if(Gate::denies('app_user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->ensureRestaurantOwner($owner);

        $owner->update([
            'status' => (int) $owner->status === 1 ? 0 : 1,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Restaurant owner account status updated.');
    }

    private function ensureRestaurantOwner(AppUser $owner): void
    {
        abort_if($owner->user_type !== 'restaurant_owner', Response::HTTP_NOT_FOUND, 'Restaurant owner not found');
    }

    private function walletSummary(int $ownerId): array
    {
        $summary = VendorWallet::query()
            ->where('vendor_id', $ownerId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END), 0) as total_credit,
                COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END), 0) as total_debit
            ")
            ->first();

        $pending = Payout::query()
            ->where('vendorid', $ownerId)
            ->where('module', 3)
            ->whereIn(DB::raw('LOWER(payout_status)'), ['pending', 'processing'])
            ->sum('amount');

        $credit = (float) ($summary->total_credit ?? 0);
        $debit = (float) ($summary->total_debit ?? 0);
        $balance = $credit - $debit;

        return [
            'total_credit' => $credit,
            'total_debit' => $debit,
            'pending_withdrawal' => (float) $pending,
            'available_balance' => max(0, $balance - (float) $pending),
            'wallet_balance' => $balance,
        ];
    }
}
