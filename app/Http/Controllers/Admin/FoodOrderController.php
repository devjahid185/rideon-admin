<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modern\FoodOrder;
use App\Models\Modern\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FoodOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $restaurantId = $request->input('restaurant_id');
        $from = $request->input('from');
        $to = $request->input('to');

        $query = FoodOrder::query()
            ->with([
                'customer:id,first_name,last_name,phone,phone_country',
                'driver:id,first_name,last_name,phone,phone_country',
                'restaurant:id,name',
                'branch:id,name,address',
            ])
            ->latest('id');

        if (! empty($status)) {
            $query->where('status', $status);
        }
        if (! empty($restaurantId)) {
            $query->where('restaurant_id', $restaurantId);
        }
        if (! empty($from)) {
            $query->whereDate('created_at', '>=', $from);
        }
        if (! empty($to)) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->paginate(30)->appends($request->query());

        $statusCounts = FoodOrder::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $statusCounts['all'] = array_sum($statusCounts);

        $restaurants = Restaurant::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.foodOrders.index', compact(
            'orders',
            'status',
            'restaurantId',
            'restaurants',
            'statusCounts'
        ));
    }

    public function show(int $id)
    {
        $order = FoodOrder::query()
            ->with([
                'customer:id,first_name,last_name,email,phone,phone_country',
                'driver:id,first_name,last_name,email,phone,phone_country',
                'restaurant:id,name,slug,rating',
                'branch:id,name,address,latitude,longitude',
                'items.foodItem:id,name,base_price',
                'items.variant:id,name,price_delta',
                'statusLogs.changedBy:id,first_name,last_name,user_type',
                'locationTracks',
            ])
            ->findOrFail($id);

        $nextStatuses = $this->allowedNextStatuses((string) $order->status);
        $trackingPoints = $order->locationTracks()
            ->orderBy('id')
            ->limit(500)
            ->get(['latitude', 'longitude', 'heading', 'speed', 'accuracy', 'recorded_at', 'created_at']);

        return view('admin.foodOrders.show', compact('order', 'nextStatuses', 'trackingPoints'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string|max:500',
        ]);

        $order = FoodOrder::findOrFail($id);
        $oldStatus = (string) $order->status;
        $newStatus = trim((string) $request->input('status'));
        $allowed = $this->allowedNextStatuses($oldStatus);

        if (! in_array($newStatus, $allowed, true)) {
            return redirect()
                ->back()
                ->with('error', "Invalid status transition from {$oldStatus} to {$newStatus}");
        }

        $order->status = $newStatus;
        if ($newStatus === FoodOrder::STATUS_PICKED_UP) {
            $order->picked_up_at = now();
        }
        if ($newStatus === FoodOrder::STATUS_DELIVERED) {
            $order->delivered_at = now();
            if ($order->payment_status === 'pending' && $order->payment_method === 'cash_on_delivery') {
                $order->payment_status = 'paid';
            }
        }
        $order->save();

        $order->statusLogs()->create([
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'changed_by_user_id' => optional(auth()->user())->id,
            'note' => $request->input('note'),
        ]);

        Log::info('Admin food order status updated from panel', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'admin_user_id' => optional(auth()->user())->id,
        ]);

        return redirect()
            ->route('admin.food-orders.show', $order->id)
            ->with('success', 'Food order status updated successfully');
    }

    public function tracking(int $id)
    {
        $order = FoodOrder::findOrFail($id);
        $points = $order->locationTracks()
            ->orderBy('id')
            ->limit(500)
            ->get(['latitude', 'longitude', 'heading', 'speed', 'accuracy', 'recorded_at', 'created_at']);

        return response()->json([
            'status' => 200,
            'data' => [
                'tracking_active' => in_array($order->status, [
                    FoodOrder::STATUS_PICKED_UP,
                    FoodOrder::STATUS_ON_THE_WAY,
                ], true),
                'route_points' => $points,
                'latest' => $points->last(),
            ],
        ]);
    }

    private function allowedNextStatuses(string $currentStatus): array
    {
        return match ($currentStatus) {
            FoodOrder::STATUS_PLACED => [FoodOrder::STATUS_ACCEPTED, FoodOrder::STATUS_CANCELLED],
            FoodOrder::STATUS_ACCEPTED => [FoodOrder::STATUS_PREPARING, FoodOrder::STATUS_CANCELLED],
            FoodOrder::STATUS_PREPARING => [FoodOrder::STATUS_READY_FOR_PICKUP, FoodOrder::STATUS_CANCELLED],
            FoodOrder::STATUS_READY_FOR_PICKUP => [FoodOrder::STATUS_PICKED_UP, FoodOrder::STATUS_CANCELLED],
            FoodOrder::STATUS_PICKED_UP => [FoodOrder::STATUS_ON_THE_WAY],
            FoodOrder::STATUS_ON_THE_WAY => [FoodOrder::STATUS_DELIVERED],
            default => [],
        };
    }
}
