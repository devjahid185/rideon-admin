<?php

namespace App\Models\Modern;

use App\Models\GeneralSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'currency';

    public const STATUS_SELECT = [
        '1' => 'Active',
        '0' => 'InActive',
    ];

    // The attributes that are mass assignable.
    protected $fillable = [
        'currency_name',
        'currency_code',
        'status',
        'value_against_default_currency',
        'currency_symbol',
    ];

    // The attributes that should be hidden for arrays.
    protected $hidden = [];

    // The attributes that should be cast to native types.
    protected $casts = [];

    public static function getValueByCurrencyCode($currencyCode)
    {

        $keys = [
            'multicurrency_status',
        ];

        $settings = GeneralSetting::whereIn('meta_key', $keys)->get()->keyBy('meta_key');

        $multicurrencyStatus = $settings->get('multicurrency_status');

        if ($multicurrencyStatus && $multicurrencyStatus->meta_value == 0) {
            return 1;
        }

        $currency = self::where('currency_code', $currencyCode)->first();

        return $currency ? $currency->value_against_default_currency : 1;
    }
}
