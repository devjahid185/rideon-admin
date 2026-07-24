<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Modern\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyApiController extends Controller
{
    /**
     * Display a listing of currencies.
     */
    public function index(): JsonResponse
    {
        $currencies = Currency::select('id', 'currency_name', 'currency_code', 'value_against_default_currency', 'currency_symbol')->get();

        return response()->json([
            'success' => true,
            'data' => $currencies,
        ]);
    }

    public function updateRates()
    {
        $metaKeys = [
            'general_default_currency',
            'currency_auth_key',
        ];

        $settings = GeneralSetting::whereIn('meta_key', $metaKeys)->get()->keyBy('meta_key');
        $general_default_currency = $settings['general_default_currency']->meta_value ?? null;
        $currency_API = $settings['currency_auth_key']->meta_value ?? null;

        $apiUrl = 'https://v6.exchangerate-api.com/v6/'.$currency_API.'/latest/'.$general_default_currency;

        $response = file_get_contents($apiUrl);
        $data = json_decode($response, true);

        if ($data['result'] === 'success') {
            $conversionRates = $data['conversion_rates'];

            foreach ($conversionRates as $currencyCode => $rate) {
                $currency = Currency::where('currency_code', $currencyCode)->first();

                if ($currency && $currency->currency_name) {
                    $currency->value_against_default_currency = $rate;
                    $currency->save();
                }
            }

            return response()->json(['message' => 'Currency rates updated successfully']);
        } else {
            return response()->json(['message' => 'API request failed'], 500);
        }
    }
}
