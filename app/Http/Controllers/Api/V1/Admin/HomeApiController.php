<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\BookingAvailableTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\ResponseTrait;

class HomeApiController extends Controller
{
    use BookingAvailableTrait, MediaUploadingTrait, MiscellaneousTrait, ResponseTrait;

    /**
     * Get home data.
     *
     * @return \Illuminate\Http\Response
     */
}
