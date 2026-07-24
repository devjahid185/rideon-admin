<?php

namespace App\Jobs;

use App\Models\AppUser;
use App\Models\Booking;
use App\Models\VendorWallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DistributeVendorCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        try {
            DB::transaction(function () {
                $currentDate = Carbon::now();

                Booking::where('bookings.status', 'Completed')
                    ->where('bookings.vendor_commission_given', 0)
                    ->join('app_users', 'bookings.host_id', '=', 'app_users.id')
                    ->select('bookings.*')
                    ->chunkById(
                        100,
                        function ($bookings) use ($currentDate) {
                            $walletInserts = [];
                            $updateIds = [];

                            foreach ($bookings as $booking) {
                                $vendorId = $booking->host_id;

                                if (! AppUser::where('id', $vendorId)->exists()) {
                                    Log::warning("Skipped wallet entries: Vendor ID {$vendorId} does not exist for Booking #{$booking->token}");

                                    continue;
                                }

                                $fullAmount = $booking->total ?? 0;
                                $adminCommission = $booking->admin_commission ?? 0;
                                $vendorCommission = $booking->vendor_commission ?? 0;
                                $discountAmount = $booking->discount_price ?? 0;

                                if ($fullAmount <= 0) {
                                    Log::warning("Skipped booking ID {$booking->id}: total amount invalid ({$fullAmount})");

                                    continue;
                                }

                                $generateWalletToken = function () {
                                    do {
                                        $token = Str::upper(Str::random(10));
                                    } while (VendorWallet::where('token', $token)->exists());

                                    return $token;
                                };

                                $paymentType = $booking->payment_method === 'cash' ? 'cash' : 'online';

                                $walletToken = $generateWalletToken();
                                $walletInserts[] = [
                                    'vendor_id' => $vendorId,
                                    'amount' => $fullAmount,
                                    'booking_id' => $booking->id,
                                    'type' => 'credit',
                                    'token' => $walletToken,
                                    'description' => "Full booking amount (+{$fullAmount}) credited for {$paymentType} booking #{$booking->token}, Wallet #{$walletToken}",
                                    'created_at' => $currentDate,
                                    'updated_at' => $currentDate,
                                ];

                                if ($adminCommission > 0) {
                                    $walletToken = $generateWalletToken();
                                    $walletInserts[] = [
                                        'vendor_id' => $vendorId,
                                        'amount' => $adminCommission,
                                        'booking_id' => $booking->id,
                                        'type' => 'debit',
                                        'token' => $walletToken,
                                        'description' => "Admin commission (-{$adminCommission}) for {$paymentType} booking #{$booking->token}, Wallet #{$walletToken}",
                                        'created_at' => $currentDate,
                                        'updated_at' => $currentDate,
                                    ];
                                }

                                if ($discountAmount > 0) {
                                    $walletToken = $generateWalletToken();
                                    $walletInserts[] = [
                                        'vendor_id' => $vendorId,
                                        'amount' => $discountAmount,
                                        'booking_id' => $booking->id,
                                        'type' => 'credit',
                                        'token' => $walletToken,
                                        'description' => "Discount amount ({$discountAmount}) paid by admin for booking #{$booking->token}, Wallet #{$walletToken}",
                                        'created_at' => $currentDate,
                                        'updated_at' => $currentDate,
                                    ];
                                }

                                if ($booking->payment_method === 'cash' && $vendorCommission > 0) {
                                    $walletToken = $generateWalletToken();
                                    $totalDebit = $adminCommission + $vendorCommission - $discountAmount;
                                    $walletInserts[] = [
                                        'vendor_id' => $vendorId,
                                        'amount' => $totalDebit,
                                        'booking_id' => $booking->id,
                                        'type' => 'debit',
                                        'token' => $walletToken,
                                        'description' => "Cash booking adjustment: Admin + Vendor commission (-{$totalDebit}) for booking #{$booking->token}, Wallet #{$walletToken}",
                                        'created_at' => $currentDate,
                                        'updated_at' => $currentDate,
                                    ];
                                }

                                $updateIds[] = $booking->id;
                            }

                            if ($walletInserts) {
                                VendorWallet::insert($walletInserts);
                            }

                            if ($updateIds) {
                                Booking::whereIn('id', $updateIds)->update([
                                    'vendor_commission_given' => 1,
                                    'updated_at' => $currentDate,
                                ]);
                            }
                        },
                        'bookings.id',
                        'id'
                    );
            });

            Log::info('✅ Vendor wallet updated successfully');
        } catch (\Throwable $e) {
            Log::error('❌ Vendor wallet update failed: '.$e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
}
