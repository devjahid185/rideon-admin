<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Requests\UpdateGeneralSettingRequest;
use App\Http\Resources\Admin\GeneralSettingResource;
use App\Models\GeneralSetting;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class GeneralSettingApiController extends Controller
{
    use MediaUploadingTrait, MiscellaneousTrait, ResponseTrait;

    public function index()
    {
        abort_if(Gate::denies('general_setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new GeneralSettingResource(GeneralSetting::all());
    }

    public function update(UpdateGeneralSettingRequest $request, GeneralSetting $generalSetting)
    {
        $generalSetting->update($request->all());

        return (new GeneralSettingResource($generalSetting))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function getgeneralSettings(Request $request)
    {
        try {
            $module = $this->getModuleIdOrDefault($request);
            $domain = rtrim(env('APP_URL'), '/').'/';
            $cacheMinutes = 120;
            $cacheKey = 'general_settings_'.$module;

            if ($request->has('refresh_cache') && $request->refresh_cache == true) {
                Cache::forget($cacheKey);
            }

            $metaData = Cache::remember($cacheKey, $cacheMinutes, function () use ($module) {
                $keys = [
                    'general_default_phone_country',
                    'general_default_country_code',
                    'general_default_currency',
                    'general_default_language',
                    'socialnetwork_google_login',
                    'onlinepayment',
                    'firebase_update_interval',
                    'location_accuracy_threshold',
                    'background_location_interval',
                    'driver_search_interval',
                    'use_google_after_pickup',
                    'use_google_before_pickup',
                    'minimum_hits_time',
                    'use_google_source_destination',
                ];

                $metaData = GeneralSetting::whereIn('meta_key', $keys)
                    ->pluck('meta_value', 'meta_key')
                    ->toArray();

                $otherInfoKeys = ['title', 'item_setting_image'];
                $otherInfoData = GeneralSetting::whereIn('meta_key', $otherInfoKeys)
                    ->where('module', $module)
                    ->pluck('meta_value', 'meta_key')
                    ->toArray();

                return array_merge($metaData, $otherInfoData);
            });
            $metaData['last_active'] = '0';
            $metaData['minimum_negative_balance'] = '9';

            return $this->addSuccessResponse(200, trans('front.Result_found'), ['metaData' => $metaData]);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('front.something_wrong'));
        }
    }
}
