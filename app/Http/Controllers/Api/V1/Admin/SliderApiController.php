<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\Slider;
use Illuminate\Support\Facades\Log;

class SliderApiController extends Controller
{
    use MediaUploadingTrait,ResponseTrait;

    public function sliders()
    {
        $sliders = Slider::where('status', '1')->get();

        $data = $sliders->map(function ($slider) {
            $firstImage = $slider->image->first();
            return [
                'id' => $slider->id,
                'heading' => $slider->heading,
                'url' => $slider->url,
                'image' => $firstImage ? $firstImage->getUrl() : null,
            ];
        })->toArray();

        Log::info('sliders api response prepared', [
            'total_active_sliders' => $sliders->count(),
            'with_image' => collect($data)->whereNotNull('image')->count(),
            'without_image' => collect($data)->whereNull('image')->count(),
        ]);

        return response()->json([
            'status' => 200,
            'message' => trans('global.slider_data'),
            'data' => $data,
        ]);

    }
}
