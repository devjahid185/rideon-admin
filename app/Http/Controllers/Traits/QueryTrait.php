<?php

namespace App\Http\Controllers\Traits;

use App\Models\GeneralSetting;

trait QueryTrait
{
    public function get_general_setting($meta_key)
    {
        return $general_setting_id = GeneralSetting::where('meta_key', $meta_key)
            ->first();
    }
}
