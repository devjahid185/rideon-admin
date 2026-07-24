<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\EmailTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\NotificationTrait;
use App\Http\Controllers\Traits\PushNotificationTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Controllers\Traits\SMSTrait;
use App\Http\Controllers\Traits\UserWalletTrait;
use App\Http\Controllers\Traits\VendorWalletTrait;
use App\Models\AppUser;
use App\Models\Booking;
use App\Models\GeneralSetting;
use App\Models\Modern\Item;
use App\Models\Module;

class FinanceController extends Controller
{
    use EmailTrait,MediaUploadingTrait,NotificationTrait,PushNotificationTrait,ResponseTrait,SMSTrait,UserWalletTrait,VendorWalletTrait;

    public function index()
    {
        $request = request();

        $from = $request->input('from');
        $to = $request->input('to');
        $item = $request->input('item');
        $vendor = $request->input('vendor');
        $admin = $request->input('admin');
        $status = $request->input('status');

        $currentModule = Module::where('default_module', 1)->first();

        // Base query with eager loading to avoid N+1 problem
        $query = Booking::with([
            'host:id,first_name,last_name',
            'user:id,first_name,last_name',
            'item:id,title', // Optional: if you use item in the view
        ])
            ->where('payment_status', 'paid')
            ->whereIn('status', ['Pending', 'Cancelled', 'Confirmed', 'Completed']);

        // Filter by date range
        if ($from && $to) {
            $query->whereBetween('bookings.created_at', ["$from 00:00:00", "$to 23:59:59"]);
        } elseif ($from) {
            $query->where('bookings.created_at', '>=', "$from 00:00:00");
        } elseif ($to) {
            $query->where('bookings.created_at', '<=', "$to 23:59:59");
        }

        // Status filter
        if (in_array($status, ['pending', 'confirmed', 'cancelled', 'completed'])) {
            $query->where('bookings.status', $status);
        }

        // Vendor, Admin, Item filters
        if ($vendor) {
            $query->where('userid', $vendor);
        }
        if ($admin) {
            $query->where('host_id', $admin);
        }
        if ($item) {
            $query->where('itemid', $item);
        }

        // Clone for summary metrics
        $totalsQuery = clone $query;

        $total_bookings = $totalsQuery->count();
        $total_earnings = $totalsQuery->sum('total');
        $total_online_payment = (clone $totalsQuery)->where('payment_method', '!=', 'cash')->sum('total');
        $total_cash_payment = (clone $totalsQuery)->where('payment_method', 'cash')->sum('total');
        $admin_commission = $totalsQuery->sum('admin_commission');
        $vendor_commission = (clone $totalsQuery)->where('vendor_commission_given', 1)->sum('vendor_commission');

        // Paginate results
        $bookings = $query->orderByDesc('id')->paginate(50);
        $bookings->appends($request->only(['from', 'to', 'status', 'vendor', 'admin', 'item']));

        // Load search names
        $vendorUser = AppUser::find($vendor);
        $adminUser = AppUser::find($admin);
        $itemData = Item::find($item);

        $vendorsearch = $vendorUser->first_name ?? 'All';
        $vendorsearchId = $vendorUser->id ?? '';

        $adminsearch = $adminUser->first_name ?? 'All';
        $adminsearchId = $adminUser->id ?? '';

        $searchfieldItem = $itemData->title ?? 'All';
        $searchfieldItemId = $itemData->id ?? '';

        $general_default_currency = GeneralSetting::where('meta_key', 'general_default_currency')->first();

        return view('admin.finance.index', compact(
            'bookings',
            'admin_commission',
            'vendor_commission',
            'total_earnings',
            'total_online_payment',
            'total_cash_payment',
            'total_bookings',
            'searchfieldItem',
            'searchfieldItemId',
            'vendorsearch',
            'vendorsearchId',
            'general_default_currency',
            'adminsearch',
            'adminsearchId',
            'currentModule'
        ));
    }

    public function ticketDeleteAll(Request $request)
    {
        $ids = $request->input('ids');

        if (! empty($ids)) {
            try {
                Booking::whereIn('id', $ids)->delete();

                return response()->json(['message' => 'Items deleted successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }

    }
}
