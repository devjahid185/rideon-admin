<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentMethodController extends Controller
{
    protected $paymentMethods = [
        'paypal' => [
            'meta_keys' => [
                'test_paypal_client_id',
                'test_paypal_secret_key',
                'live_paypal_client_id',
                'live_paypal_secret_key',
                'paypal_options',
                'paypal_status',
                'onlinepayment',
            ],
            'view' => 'admin.generalSettings.payment-methods.form',
            'options_field' => 'paypal_options',
            'status_field' => 'paypal_status',
            'title' => 'PayPal',
            'fields' => ['client_id', 'secret_key'],
        ],
        'stripe' => [
            'meta_keys' => [
                'test_stripe_public_key',
                'test_stripe_secret_key',
                'live_stripe_public_key',
                'live_stripe_secret_key',
                'stripe_options',
                'stripe_status',
                'onlinepayment',
            ],
            'view' => 'admin.generalSettings.payment-methods.form',
            'options_field' => 'stripe_options',
            'status_field' => 'stripe_status',
            'title' => 'Stripe',
            'fields' => ['public_key', 'secret_key'],
        ],
        'razorpay' => [
            'meta_keys' => [
                'test_razorpay_key_id',
                'test_razorpay_secret_key',
                'live_razorpay_key_id',
                'live_razorpay_secret_key',
                'razorpay_options',
                'razorpay_status',
                'onlinepayment',
            ],
            'view' => 'admin.generalSettings.payment-methods.form',
            'options_field' => 'razorpay_options',
            'status_field' => 'razorpay_status',
            'title' => 'Razorpay',
            'fields' => ['key_id', 'secret_key'],
        ],
        'transbank' => [
            'meta_keys' => [
                'test_transbank_client_id',
                'test_transbank_secret_key',
                'live_transbank_client_id',
                'live_transbank_secret_key',
                'transbank_options',
                'transbank_status',
                'onlinepayment',
            ],
            'view' => 'admin.generalSettings.payment-methods.form',
            'options_field' => 'transbank_options',
            'status_field' => 'transbank_status',
            'title' => 'Transbank',
            'fields' => ['client_id', 'secret_key'],
        ],
        'paystack' => [
            'meta_keys' => [
                'test_paystack_public_key',
                'test_paystack_secret_key',
                'live_paystack_public_key',
                'live_paystack_secret_key',
                'paystack_options',
                'paystack_status',
                'onlinepayment',
            ],
            'view' => 'admin.generalSettings.payment-methods.form',
            'options_field' => 'paystack_options',
            'status_field' => 'paystack_status',
            'title' => 'Paystack',
            'fields' => ['public_key', 'secret_key'],
        ],
        'flutterwave' => [
            'meta_keys' => [
                'test_flutterwave_public_key',
                'test_flutterwave_secret_key',
                'live_flutterwave_public_key',
                'live_flutterwave_secret_key',
                'flutterwave_options',
                'flutterwave_status',
                'onlinepayment',
            ],
            'view' => 'admin.generalSettings.payment-methods.form',
            'options_field' => 'flutterwave_options',
            'status_field' => 'flutterwave_status',
            'title' => 'Flutterwave',
            'fields' => ['public_key', 'secret_key'],
        ],
        'paydunya' => [
            'meta_keys' => [
                'test_paydunya_master_key',
                'test_paydunya_private_key',
                'test_paydunya_status',
                'test_paydunya_token',
                'live_paydunya_master_key',
                'live_paydunya_private_key',
                'live_paydunya_status',
                'live_paydunya_token',
                'paydunya_options',
                'paydunya_status',
                'onlinepayment',
            ],
            'view' => 'admin.generalSettings.payment-methods.form',
            'options_field' => 'paydunya_options',
            'status_field' => 'paydunya_status',
            'title' => 'Paydunya',
            'fields' => ['master_key', 'private_key', 'token'],
        ],
        'cash' => [
            'meta_keys' => [
                'cash_status',
                'onlinepayment',
            ],
            'view' => 'admin.generalSettings.payment-methods.form',
            'options_field' => null,
            'status_field' => 'cash_status',
            'title' => 'Cash',
            'fields' => [],
        ],
    ];

    public function index($method)
    {

        abort_if(Gate::denies('general_setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (! isset($this->paymentMethods[$method])) {
            abort(404, 'Payment method not found');
        }

        $config = $this->paymentMethods[$method];
        $settings = GeneralSetting::whereIn('meta_key', $config['meta_keys'])
            ->get()
            ->keyBy('meta_key');

        $viewData = [
            'method' => $method,
            'title' => $config['title'],
            'options_field' => $config['options_field'],
            'status_field' => $config['status_field'],
            'fields_per_method' => $config['fields'] ?? [],
        ];

        foreach ($config['meta_keys'] as $key) {
            $viewData[$key] = $settings->get($key);
        }
        $status = $settings->get($config['status_field']);

        return view($config['view'], array_merge($viewData, [
            'status' => $status, // pass explicitly
        ]));
    }

    public function update(Request $request, $method)
    {
        if (Gate::denies('general_setting_edit')) {
            return redirect()->back()->with('error', 'Form submission is disabled in demo mode.');
        }

        if (! isset($this->paymentMethods[$method])) {
            return redirect()->back()->with('error', 'Payment method not found.');
        }

        $config = $this->paymentMethods[$method];
        $optionsField = $config['options_field'];
        $options = $optionsField ? $request->input($optionsField) : null;
        $excludedKeys = [];
        $formData = $request->except(array_merge(['_token'], $excludedKeys));
        foreach ($formData as $metaKey => $metaValue) {
            if (! empty($metaValue)) {
                GeneralSetting::updateOrCreate(
                    ['meta_key' => $metaKey],
                    ['meta_value' => $metaValue]
                );
            }
        }

        return redirect()->back()->with('success', 'Updated successfully');
    }

    public function updateStatus(Request $request, $method)
    {
        abort_if(Gate::denies('general_setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden'); //
        if (! isset($this->paymentMethods[$method])) {
            return response()->json([
                'status' => 400,
                'message' => trans('global.something_went_wrong'),
            ]);
        }

        $statusField = $this->paymentMethods[$method]['status_field'] ?? null;

        if (! $statusField) {
            return response()->json([
                'status' => 400,
                'message' => trans('global.something_went_wrong'),
            ]);

        }
        $status = strtolower($request->status) === '1' ? 'Active' : 'Inactive';
        GeneralSetting::updateOrCreate(
            ['meta_key' => $statusField],
            ['meta_value' => $status]
        );

        return response()->json([
            'status' => 200,
            'message' => trans('global.status_updated_successfully'),
        ]);
    }

    public function updateOnlineStatus(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return response()->json(['error' => 'Form submission is disabled in demo mode.'], 403);
        }

        $status = $request->input('status');

        GeneralSetting::updateOrCreate(
            ['meta_key' => 'onlinepayment'],
            ['meta_value' => $status]
        );

        return response()->json(['success' => 'Updated Successfully']);
    }
}
