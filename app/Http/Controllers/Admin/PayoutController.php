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
use App\Http\Requests\UpdatePayoutRequest;
use App\Models\AppUser;
use App\Models\GeneralSetting;
use App\Models\Payout;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayoutController extends Controller
{
    use EmailTrait, MediaUploadingTrait, NotificationTrait, PushNotificationTrait, ResponseTrait, SMSTrait, UserWalletTrait, VendorWalletTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('payout_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from = $request->input('from');
        $to = $request->input('to');
        $status = $request->input('status');
        $vendor = $request->input('vendor');

        $query = Payout::with(['vendor:id,first_name,last_name,phone,user_type'])
            ->whereIn('module', [2, 3])
            ->orderByDesc('id');

        if ($from || $to) {
            $fromDate = $from ? $from.' 00:00:00' : null;
            $toDate = $to ? $to.' 23:59:59' : null;

            $query->where(function ($q) use ($fromDate, $toDate) {
                if ($fromDate && $toDate) {
                    $q->whereBetween('created_at', [$fromDate, $toDate])
                        ->orWhereBetween('updated_at', [$fromDate, $toDate]);
                } elseif ($fromDate) {
                    $q->where('created_at', '>=', $fromDate)
                        ->orWhere('updated_at', '>=', $fromDate);
                } elseif ($toDate) {
                    $q->where('created_at', '<=', $toDate)
                        ->orWhere('updated_at', '<=', $toDate);
                }
            });
        }

        if ($status !== null) {
            $query->where('payout_status', $status);
        }

        if ($vendor !== null) {
            $query->where('vendorid', $vendor);
        }

        // Get summary in a single query
        $summaryQuery = (clone $query)
            ->reorder()
            ->selectRaw("
        COUNT(*) as total_payouts,
        SUM(amount) as total_amount,
        SUM(CASE WHEN payout_status = 'Pending' THEN amount ELSE 0 END) as pending_amount,
        SUM(CASE WHEN payout_status = 'Success' THEN amount ELSE 0 END) as success_amount
    ")
            ->first();

        $summary = [
            'total_payouts' => $summaryQuery->total_payouts ?? 0,
            'total_amount' => $summaryQuery->total_amount ?? 0,
            'pending_amount' => $summaryQuery->pending_amount ?? 0,
            'success_amount' => $summaryQuery->success_amount ?? 0,
        ];

        // Paginate
        $payouts = $query->paginate(50);
        $payouts->load('media'); // eager load Spatie media efficiently

        // Vendor display info
        if ($vendor) {
            $vendorModel = AppUser::select('id', 'first_name', 'last_name', 'phone')->find($vendor);
            if ($vendorModel) {
                $vendorName = "{$vendorModel->first_name} {$vendorModel->last_name} ({$vendorModel->phone})";
                $vendorId = $vendorModel->id;
            } else {
                $vendorName = 'Unknown Vendor';
                $vendorId = '';
            }
        } else {
            $vendorName = 'All';
            $vendorId = '';
        }

        return view('admin.payouts.index', compact('payouts', 'vendorName', 'vendorId', 'summary'));
    }

    public function create()
    {
        abort_if(Gate::denies('payout_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $payouts = '';

        return view('admin.payouts.create', compact('payouts'));
    }

    public function store(Request $request)
    {
        $booking = $request->vendorid;
        $payout_status = 'Pending';

        $totalmoney = Payout::where('vendorid', $booking)->where('payout_status', $payout_status)->sum('amount');

        $hostspendmoney = str_replace(',', '', $this->getVendorWalletBalance($booking));

        $withdrawalAmount = $request->amount;
        $withdrawalAmount = $withdrawalAmount + $totalmoney;

        if ($withdrawalAmount > $hostspendmoney) {

            $errorMessage = 'Withdrawal amount is greater than your available balance';

            return redirect()->back()->with('error', $errorMessage);
        }
        $requestData = $request->all();
        $requestData['request_by'] = 'admin';
        $requestData['payout_status'] = $payout_status;

        $payout = Payout::create($requestData);
        if ($request->input('payout_proof', false)) {
            $payout->addMedia(storage_path('tmp/uploads/'.basename($request->input('payout_proof'))))->toMediaCollection('payout_proof');
        }

        $vendorId = $payout->vendorid;
        $amount = $payout->amount;
        $currency = $payout->currency;
        $bookingId = null;
        $payoutId = $payout->id;
        $type = 'debit';
        $description = 'Payout Status Update';

        return redirect()->route('admin.payouts.index');
    }

    // payment approved function
    public function updateStatus(Request $request, $payout)
    {

        $payoutData = Payout::find($payout);
        if (! $payoutData) {
            return response()->json(['error' => 'Payout not found'], 404);
        }

        if ($request->hasFile('payout_proof')) {
            $payoutData->clearMediaCollection('payout_proof'); // clear old proof if any
            $payoutData->addMedia($request->file('payout_proof'))->toMediaCollection('payout_proof');
        }

        $newStatus = 'Success';
        $payoutData->payout_status = $newStatus;
        if ($request->filled('note')) {
            $payoutData->note = $request->input('note');
        }
        $payoutData->save();

        $vendorId = $payoutData->vendorid;
        $amount = $payoutData->amount;
        $currency = $payoutData->currency;
        $bookingId = null;
        $payoutId = $payout;
        $type = 'debit';
        $description = 'Payout Status Update';

        $this->addVendorWalletTransaction($vendorId, $amount, $type, $bookingId, $payoutId, $description);

        $user = AppUser::where('id', $vendorId)->first();
        $settings = GeneralSetting::whereIn('meta_key', ['general_email'])
            ->get()
            ->keyBy('meta_key');

        $general_email = $settings['general_email'] ?? null;

        $template_id = 6;
        $valuesArray = $user->toArray();
        $valuesArray = $user->only(['first_name', 'last_name', 'email', 'phone_country', 'phone']);
        $valuesArray['phone'] = $valuesArray['phone_country'].$valuesArray['phone'];
        $valuesArray['currency_code'] = $currency;
        $valuesArray['payout_amount'] = $amount;
        $valuesArray['payout_bank'] = $payoutData->payment_method;
        $valuesArray['support_email'] = $general_email->meta_value;
        $valuesArray['payout_date'] = now()->format('Y-m-d');
        $this->sendAllNotifications($valuesArray, $user->id, $template_id);

        return response()->json([
            'ststus' => 200,
            'message' => 'Payout Status updated successfully.',
        ]);
    }

    public function rejectPayout(Request $request)
    {
        $request->validate([
            'payout_id' => 'required|exists:payouts,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $payout = Payout::find($request->input('payout_id'));
        if (! $payout) {
            return response()->json(['error' => 'Payout not found'], 404);
        }

        if ($payout->payout_status !== 'Pending') {
            return response()->json(['error' => 'Only pending payouts can be rejected'], 422);
        }

        $payout->payout_status = 'Rejected';
        if ($request->filled('note')) {
            $payout->note = $request->input('note');
        }
        $payout->save();

        return response()->json([
            'status' => 200,
            'message' => 'Payout rejected successfully.',
        ]);
    }

    public function edit(Payout $payout)
    {
        abort_if(Gate::denies('payout_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.payouts.edit', compact('payout'));
    }

    public function update(UpdatePayoutRequest $request, Payout $payout)
    {
        $payout->update($request->all());

        return redirect()->route('admin.payouts.index');
    }

    public function show(Payout $payout)
    {
        abort_if(Gate::denies('payout_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.payouts.show', compact('payout'));
    }

    public function payoutVendorSearch(Request $request)
    {
        $searchTerm = $request->input('q');

        $drivers = AppUser::whereIn('user_type', ['driver', 'restaurant_owner'])
            ->where(function ($query) use ($searchTerm) {
                $query->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('last_name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('phone', 'like', "%{$searchTerm}%");
            })
            ->select('id', 'first_name', 'last_name', 'phone', 'user_type')
            ->get()
            ->map(function ($user) {
                $role = $user->user_type === 'restaurant_owner' ? 'Restaurant Owner' : 'Driver';

                return [
                    'id' => $user->id,
                    'name' => "{$user->first_name} {$user->last_name} ({$user->phone}) - {$role}",
                ];
            });

        return response()->json($drivers);
    }

    public function getWalletBalance($vendorId)
    {

        $balance = $this->getVendorWalletBalance($vendorId);

        return response()->json([
            'wallet_balance' => $balance,
        ]);
    }
}
