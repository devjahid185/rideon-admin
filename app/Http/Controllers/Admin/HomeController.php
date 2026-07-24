<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppUser;
use App\Models\Booking;
use App\Models\GeneralSetting;
use App\Models\Modern\Item;
use App\Models\Module;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class HomeController
{
    public function index()
    {

        $module = Cache::remember('default_module', 3600, function () {
            return Module::where('default_module', '1')->firstOrFail();
        });

        $moduleId = $module->id;
        $moduleName = $module->name;
        $currency = Cache::remember('general_default_currency', 3600, function () {
            return GeneralSetting::where('meta_key', 'general_default_currency')->first();
        });

        $installerWarning = installerExists();
        $metrics = $this->fetchDashboardMetrics($moduleId);
        $latestBookings = Booking::with(['host', 'user', 'item'])
            ->where('module', $moduleId)
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->startOfYear())
            ->latest()
            ->take(5)
            ->get();

        $latestUsersData = $this->getLatestUsersData();
        $latestBookingsData = $this->getLatestBookingsData($moduleId);

        return view('home', compact(
            'metrics',
            'currency',
            'moduleName',
            'moduleId',
            'latestBookings',
            'latestUsersData',
            'latestBookingsData',
            'installerWarning',
        ));
    }

    private function fetchDashboardMetrics($moduleId)
    {

        $driverMetrics = Cache::remember('driver_metrics', 3600, function () {
            $today = Carbon::today()->toDateString();

            return AppUser::selectRaw("
                COUNT(*) as total_drivers,
                SUM(CASE WHEN user_type = 'driver' AND status = 1 THEN 1 ELSE 0 END) as active_drivers,
                SUM(CASE WHEN user_type = 'driver' AND status = 0 THEN 1 ELSE 0 END) as inactive_drivers,
                SUM(CASE WHEN user_type = 'driver' AND host_status = '2' THEN 1 ELSE 0 END) as requested_drivers,
                SUM(CASE WHEN user_type = 'driver' AND DATE(created_at) = ? THEN 1 ELSE 0 END) as today_new_drivers
            ", [$today])
                ->where('user_type', 'driver')
                ->first();
        });

        // Fetch ride (item) metrics
        $riderMetrics = Cache::remember('rider_metrics', 3600, function () {
            $today = Carbon::today()->toDateString();

            return AppUser::selectRaw("
        COUNT(*) as total_riders,
        SUM(CASE WHEN user_type = 'user' AND status = 1 THEN 1 ELSE 0 END) as active_riders,
        SUM(CASE WHEN user_type = 'user' AND status = 0 THEN 1 ELSE 0 END) as inactive_riders,
        SUM(CASE WHEN user_type = 'user' AND DATE(created_at) = ? THEN 1 ELSE 0 END) as today_new_riders
    ", [$today])
                ->where('user_type', 'user')
                ->first();
        });

        // Fetch booking (ride status) metrics
        // Cache::forget("booking_metrics_{$moduleId}");
        $bookingMetrics = Cache::remember("booking_metrics_{$moduleId}", 3600, function () use ($moduleId) {
            $today = Carbon::today()->toDateString();

            return Booking::selectRaw("
        COUNT(*) as total_paid_bookings,
        SUM(CASE WHEN status = 'Ongoing' THEN 1 ELSE 0 END) as running_rides,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_rides,
        SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_rides,
        SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected_rides,
        SUM(CASE WHEN status = 'Ongoing' AND DATE(created_at) = ? THEN 1 ELSE 0 END) as today_running_rides,
        SUM(CASE WHEN status = 'Completed' AND DATE(created_at) = ? THEN 1 ELSE 0 END) as today_completed_rides,
        SUM(CASE WHEN payment_status = 'paid' AND module = ? AND deleted_at IS NULL THEN total ELSE 0 END) as total_income,
        SUM(CASE WHEN payment_status = 'paid' AND module = ? AND deleted_at IS NULL THEN admin_commission ELSE 0 END) as total_revenue,
        SUM(CASE WHEN payment_status = 'paid' AND module = ? AND deleted_at IS NULL AND DATE(created_at) = ? THEN admin_commission ELSE 0 END) as today_revenue
    ", [
                $today,        // for today_running_rides
                $today,        // for today_completed_rides
                $moduleId,     // for total_income
                $moduleId,     // for total_revenue
                $moduleId,     // for today_revenue (module check)
                $today,         // for today_revenue (date check)
            ])
                ->where('module', $moduleId)
                ->first();
        });

        return [
            'total_drivers' => [
                'chart_title' => 'total drivers',
                'total_number' => $driverMetrics->total_drivers,
            ],
            'total_active_drivers' => [
                'chart_title' => 'total active drivers',
                'total_number' => $driverMetrics->active_drivers,
            ],
            'total_inactive_drivers' => [
                'chart_title' => 'total inactive drivers',
                'total_number' => $driverMetrics->inactive_drivers,
            ],
            'total_requested_drivers' => [
                'chart_title' => 'total requested drivers',
                'total_number' => $driverMetrics->requested_drivers,
            ],
            'total_riders' => [
                'chart_title' => 'total riders',
                'total_number' => $riderMetrics->total_riders,
            ],
            'total_active_riders' => [
                'chart_title' => 'total active riders',
                'total_number' => $riderMetrics->active_riders,
            ],
            'today_new_riders' => [
                'chart_title' => 'today new riders',
                'total_number' => $riderMetrics->today_new_riders,
            ],
            'total_paid_bookings' => [
                'chart_title' => 'total paid bookings',
                'total_number' => $bookingMetrics->total_paid_bookings,
            ],
            'running_rides' => [
                'chart_title' => 'running rides',
                'total_number' => $bookingMetrics->running_rides,
            ],
            'completed_rides' => [
                'chart_title' => 'completed rides',
                'total_number' => $bookingMetrics->completed_rides,
            ],
            'cancelled_rides' => [
                'chart_title' => 'cancelled rides',
                'total_number' => $bookingMetrics->cancelled_rides,
            ],
            'rejected_rides' => [
                'chart_title' => 'rejected rides',
                'total_number' => $bookingMetrics->rejected_rides,
            ],
            'today_new_drivers' => [
                'chart_title' => 'today new drivers',
                'total_number' => $driverMetrics->today_new_drivers,
            ],
            'today_running_rides' => [
                'chart_title' => 'today running rides',
                'total_number' => $bookingMetrics->today_running_rides,
            ],
            'today_completed_rides' => [
                'chart_title' => 'today completed rides',
                'total_number' => $bookingMetrics->today_completed_rides,
            ],
            'total_revenue' => [
                'chart_title' => 'total revenue',
                'total_number' => $bookingMetrics->total_revenue,
            ],
            'today_revenue' => [
                'chart_title' => 'todays revenue',
                'total_number' => $bookingMetrics->today_revenue,
            ],
            'total_income' => [
                'chart_title' => 'total income',
                'total_number' => $bookingMetrics->total_income,
            ],
        ];
    }

    private function getLatestUsersData()
    {
        return Cache::remember('latest_users_data', 3600, function () {
            $startDate = Carbon::now()->subWeek()->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            return AppUser::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($record) {
                    return [
                        'date' => $record->date,
                        'count' => $record->count,
                    ];
                });
        });
    }

    private function getLatestBookingsData($moduleId)
    {
        return Cache::remember("latest_bookings_data_{$moduleId}", 3600, function () use ($moduleId) {
            $startDate = Carbon::now()->subWeek()->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            return Booking::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('module', $moduleId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($record) {
                    return [
                        'date' => $record->date,
                        'count' => $record->count,
                    ];
                });
        });
    }

    public function deleteInstaller()
    {
        $files = [
            app_path('Http/Controllers/InstallerController.php'),
            base_path('installer/rideon.sql'),
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $installerViewDir = resource_path('views/installer');

        if (is_dir($installerViewDir)) {
            $this->deleteDirectory($installerViewDir);
        }

        return back()->with('success', 'Installer files deleted successfully.');
    }

    private function deleteDirectory($dir)
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$item;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }
}
