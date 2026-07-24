<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\NotificationTrait;
use App\Http\Controllers\Traits\PushNotificationTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Requests\UpdateGeneralSettingRequest;
use App\Models\AddCoupon;
use App\Models\AppUser;
use App\Models\Booking;
use App\Models\City;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\Media;
use App\Models\Modern\Currency;
use App\Models\Modern\Item;
use App\Models\Modern\ItemFeatures;
use App\Models\Modern\ItemType;
use App\Models\PersonalAccessToken;
use App\Models\RentalItemRule;
use App\Models\Review;
use App\Models\VehicleMake;
use App\Models\VehicleOdometer;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class GeneralSettingController extends Controller
{
    use MediaUploadingTrait, NotificationTrait, PushNotificationTrait, ResponseTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('general_setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = GeneralSetting::query()->select(sprintf('%s.*', (new GeneralSetting)->table));
            $table = Datatables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');
            $table->editColumn('actions', function ($row) {
                $viewGate = 'general_setting_show';
                $editGate = 'general_setting_edit';
                $deleteGate = 'general_setting_delete';
                $crudRoutePart = 'general-settings';

                return view(
                    'partials.datatablesActions',
                    compact(
                        'viewGate',
                        'editGate',
                        'deleteGate',
                        'crudRoutePart',
                        'row'
                    )
                );
            });
            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('meta_key', function ($row) {
                return $row->meta_key ? $row->meta_key : '';
            });
            $table->editColumn('meta_value', function ($row) {
                return $row->meta_value ? $row->meta_value : '';
            });
            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.generalSettings.index');
    }

    public function edit(GeneralSetting $generalSetting)
    {
        abort_if(Gate::denies('general_setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.generalSettings.edit', compact('generalSetting'));
    }

    public function update(UpdateGeneralSettingRequest $request, GeneralSetting $generalSetting)
    {
        $generalSetting->update($request->all());

        return redirect()->route('admin.general-settings.index');
    }

    public function generalForm()
    {
        $metaKeys = [
            'general_name',
            'general_description',
            'general_email',
            'general_phone',
            'general_default_phone_country',
            'general_default_currency',
            'general_default_language',
            'general_favicon',
            'general_logo',
        ];
        $settings = GeneralSetting::whereIn('meta_key', $metaKeys)->get()->keyBy('meta_key');
        $general_name = $settings['general_name'] ?? null;
        $general_description = $settings['general_description'] ?? null;
        $general_email = $settings['general_email'] ?? null;
        $general_phone = $settings['general_phone'] ?? null;
        $general_default_phone_country = $settings['general_default_phone_country'] ?? null; //
        $general_default_currency = $settings['general_default_currency'] ?? null;
        $general_default_language = $settings['general_default_language'] ?? null;
        $general_favicon = $settings['general_favicon'] ?? null;
        $general_logo = $settings['general_logo'] ?? null;
        $languagedata = Language::all();
        $allcurrency = Currency::where('status', 1)->get();

        return view('admin.generalSettings.general.basic-configuration-form', compact('general_name', 'general_email', 'general_phone', 'general_default_phone_country', 'general_default_currency', 'general_default_language', 'general_favicon', 'general_logo', 'allcurrency', 'languagedata', 'general_description'));
    }

    public function addConfigurationWizard(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return redirect()->back()->with('error', 'Form submission is disabled in demo mode.');
        }
        $formData = $request->except('_token', 'general_logo', 'general_favicon');
        if ($request->hasFile('general_logo')) {
            $file = $request->file('general_logo');
            $fileName = rand(10, 1000000).'.'.$file->getClientOriginalName();
            $path = $file->storeAs('logo', $fileName, 'public');
            $formData['general_logo'] = $path;
        }
        if ($request->hasFile('general_favicon')) {
            $file = $request->file('general_favicon');
            $fileName = rand(10, 1000000).'.'.$file->getClientOriginalName();
            $destinationPath = 'public/uploads/logo';
            $path = $file->storeAs('logo', $fileName, 'public');
            $formData['general_favicon'] = $path;
        }
        foreach ($formData as $metaKey => $metaValue) {
            if (! empty($metaValue)) {
                GeneralSetting::updateOrCreate(
                    ['meta_key' => $metaKey],
                    ['meta_value' => $metaValue]
                );
            }
        }

        return redirect()->route('admin.settings')->with('success', 'Updated successfully.');
    }

    public function preferences()
    {
        $personalization_row_per_page = GeneralSetting::where('meta_key', 'personalization_row_per_page')->first();
        $personalization_min_search_price = GeneralSetting::where('meta_key', 'personalization_min_search_price')->first();
        $personalization_max_search_price = GeneralSetting::where('meta_key', 'personalization_max_search_price')->first();
        $personalization_date_separator = GeneralSetting::where('meta_key', 'personalization_date_separator')->first();
        $personalization_date_format = GeneralSetting::where('meta_key', 'personalization_date_format')->first();
        $personalization_timeZone = GeneralSetting::where('meta_key', 'personalization_timeZone')->first();
        $personalization_money_format = GeneralSetting::where('meta_key', 'personalization_money_format')->first();

        return view('admin.Preferences.NotificationPreferencesForm', compact('personalization_row_per_page', 'personalization_min_search_price', 'personalization_max_search_price', 'personalization_date_separator', 'personalization_date_format', 'personalization_timeZone', 'personalization_money_format'));
    }

    public function addPersonalization(Request $request)
    {
        $formData = $request->except('_token');
        foreach ($formData as $metaKey => $metaValue) {
            if (! empty($metaValue)) {
                GeneralSetting::updateOrCreate(
                    ['meta_key' => $metaKey],
                    ['meta_value' => $metaValue]
                );
            }
        }

        return redirect()->route('admin.preferences');
    }

    public function smsSetting()
    {
        $keys = [
            'nonage_options',
            'nonage_status',
            'sms_provider_name',
            'messagewizard_sender_number',
            'auto_fill_otp',
        ];
        $settings = GeneralSetting::whereIn('meta_key', $keys)->get()->keyBy('meta_key');
        $messagewizard_key = GeneralSetting::where('meta_key', 'messagewizard_key')->first();
        $messagewizard_secret = GeneralSetting::where('meta_key', 'messagewizard_secret')->first();
        $options = $settings->get('nonage_options') ?? null;
        $status = $settings->get('nonage_status') ?? null;
        $sms_provider_name = $settings->get('sms_provider_name') ?? null;
        $messagewizard_sender_number = $settings->get('messagewizard_sender_number') ?? null;
        $auto_fill_otp = $settings->get('auto_fill_otp') ?? null;
        $id = 1;

        return view('admin.generalSettings.smssettings.nonage', compact('messagewizard_secret', 'messagewizard_key', 'options', 'status', 'id', 'sms_provider_name', 'messagewizard_sender_number', 'auto_fill_otp'));
    }

    public function smsUpdate(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return response()->json(['error' => 'Form submission is disabled in demo mode.'], 403);
        }
        $formData = $request->except('_token');
        if (isset($formData['messagewizard_status'])) {
            $formData['messagewizard_status'] = (int) ($formData['messagewizard_status'] === '1');
            GeneralSetting::updateOrCreate(
                ['meta_key' => 'messagewizard_status'],
                ['meta_value' => $formData['messagewizard_status']]
            );
            unset($formData['messagewizard_status']);
        }
        foreach ($formData as $metaKey => $metaValue) {
            // Skip empty meta values
            if (! empty($metaValue)) {
                GeneralSetting::updateOrCreate(
                    ['meta_key' => $metaKey],
                    ['meta_value' => $metaValue]
                );
            }
        }

        return response()->json(['message' => trans('global.data_has_been_submitted')], 200);
    }

    public function Msg91()
    {
        $keys = [
            'msg91_auth_key',
            'msg91_template_id',
            'sms_provider_name',
            'auto_fill_otp',
        ];
        $settings = GeneralSetting::whereIn('meta_key', $keys)->get()->keyBy('meta_key');
        $msg91_auth_key = $settings->get('msg91_auth_key') ?? null;
        $msg91_template_id = $settings->get('msg91_template_id') ?? null;
        $sms_provider_name = $settings->get('sms_provider_name') ?? null;
        $auto_fill_otp = $settings->get('auto_fill_otp') ?? null;
        $id = 1;

        return view('admin.generalSettings.smssettings.msg91', compact('msg91_auth_key', 'msg91_template_id', 'id', 'sms_provider_name', 'auto_fill_otp'));
    }

    public function msg91Update(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return response()->json(['error' => 'Form submission is disabled in demo mode.'], 403);
        }
        $formData = $request->except('_token');
        foreach ($formData as $metaKey => $metaValue) {
            $existingSetting = GeneralSetting::where('meta_key', $metaKey)->first();
            if ($existingSetting) {
                $existingSetting->update(['meta_value' => $metaValue]);
            } else {
                GeneralSetting::create(['meta_key' => $metaKey, 'meta_value' => $metaValue]);
            }
        }

        return response()->json(['message' => trans('global.data_has_been_submitted')], 200);
    }

    public function twillioSetting()
    {
        $keys = [
            'twillio_options',
            'twillio_status',
            'sms_provider_name',
            'auto_fill_otp',
        ];
        $settings = GeneralSetting::whereIn('meta_key', $keys)->get()->keyBy('meta_key');
        $twillio_key = GeneralSetting::where('meta_key', 'twillio_key')->first();
        $twillio_secret = GeneralSetting::where('meta_key', 'twillio_secret')->first();
        $twillio_number = GeneralSetting::where('meta_key', 'twillio_number')->first();
        $options = $settings->get('twillio_options') ?? null;
        $status = $settings->get('twillio_status') ?? null;
        $sms_provider_name = $settings->get('sms_provider_name') ?? null;
        $auto_fill_otp = $settings->get('auto_fill_otp') ?? null;
        $id = 1;

        return view('admin.generalSettings.smssettings.twillio', compact('twillio_key', 'twillio_secret', 'twillio_number', 'options', 'status', 'id', 'sms_provider_name', 'auto_fill_otp'));
    }

    public function twillioSmsUpdate(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return response()->json(['error' => 'Form submission is disabled in demo mode.'], 403);
        }
        $formData = $request->except('_token');
        foreach ($formData as $metaKey => $metaValue) {
            $existingSetting = GeneralSetting::where('meta_key', $metaKey)->first();
            if ($existingSetting) {
                $existingSetting->update(['meta_value' => $metaValue]);
            } else {
                GeneralSetting::create(['meta_key' => $metaKey, 'meta_value' => $metaValue]);
            }
        }

        return response()->json(['message' => trans('global.data_has_been_submitted')], 200);
    }

    public function nexmoSetting()
    {
        $nexmo_key = GeneralSetting::where('meta_key', 'nexmo_key')->first();
        $nexmo_secret = GeneralSetting::where('meta_key', 'nexmo_secret')->first();

        return view('admin.generalSettings.smssettings.nexmo', compact('nexmo_key', 'nexmo_secret'));
    }

    public function UpdateNexmoSetting(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return response()->json(['error' => 'Form submission is disabled in demo mode.'], 403);
        }
        $formData = $request->except('_token');
        foreach ($formData as $metaKey => $metaValue) {
            $existingSetting = GeneralSetting::where('meta_key', $metaKey)->first();
            if ($existingSetting) {
                $existingSetting->update(['meta_value' => $metaValue]);
            } else {
                GeneralSetting::create(['meta_key' => $metaKey, 'meta_value' => $metaValue]);
            }
        }

        return response()->json(['message' => trans('global.data_has_been_submitted')], 200);
    }

    public function sinchSetting()
    {
        $settings = GeneralSetting::whereIn('meta_key', ['sinch_service_plan_id', 'sinch_api_token', 'sinch_sender_number', 'sms_provider_name', 'auto_fill_otp'])
            ->get()
            ->keyBy('meta_key');
        $sinch_service_plan_id = $settings['sinch_service_plan_id'] ?? null;
        $sinch_api_token = $settings['sinch_api_token'] ?? null;
        $sinch_sender_number = $settings['sinch_sender_number'] ?? null;
        $sms_provider_name = $settings['sms_provider_name'] ?? null;
        $auto_fill_otp = $settings->get('auto_fill_otp') ?? null;
        $id = 1;

        return view('admin.generalSettings.smssettings.sinch', compact('sinch_service_plan_id', 'sinch_api_token', 'sinch_sender_number', 'id', 'sms_provider_name', 'auto_fill_otp'));
    }

    public function sinchSmsUpdate(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return response()->json(['error' => 'Form submission is disabled in demo mode.'], 403);
        }
        $formData = $request->except('_token');
        foreach ($formData as $metaKey => $metaValue) {
            $existingSetting = GeneralSetting::where('meta_key', $metaKey)->first();
            if ($existingSetting) {
                $existingSetting->update(['meta_value' => $metaValue]);
            } else {
                GeneralSetting::create(['meta_key' => $metaKey, 'meta_value' => $metaValue]);
            }
        }

        return response()->json(['message' => trans('global.data_has_been_submitted')], 200);
    }

    public function twoFactor()
    {
        $twofactor_key = GeneralSetting::where('meta_key', 'twofactor_key')->first();
        $twofactor_secret = GeneralSetting::where('meta_key', 'twofactor_secret')->first();
        $twofactor_merchant_id = GeneralSetting::where('meta_key', 'twofactor_merchant_id')->first();
        $twofactor_authentication_token = GeneralSetting::where('meta_key', 'twofactor_authentication_token')->first();

        return view('admin.generalSettings.smssettings.twofactor', compact('twofactor_key', 'twofactor_secret', 'twofactor_merchant_id', 'twofactor_authentication_token'));
    }

    public function UpdateTwofactor(Request $request)
    {
        $formData = $request->except('_token');
        foreach ($formData as $metaKey => $metaValue) {
            $existingSetting = GeneralSetting::where('meta_key', $metaKey)->first();
            if ($existingSetting) {
                $existingSetting->update(['meta_value' => $metaValue]);
            } else {
                GeneralSetting::create(['meta_key' => $metaKey, 'meta_value' => $metaValue]);
            }
        }

        return redirect()->route('admin.twofactor');
    }

    public function emailSetting()
    {

        return view('admin.generalSettings.email-setting.index');
    }

    public function fees()
    {
        $metaKeys = [
            'feesetup_guest_service_charge',
            'feesetup_iva_tax',
            'feesetup_accomodation_tax',
            'feesetup_admin_commission',
            'feesetup_accomodation_tax_get',
            'feesetup_iva_tax_get',
            'feesetup_guest_service_charge_get',
        ];
        $settings = GeneralSetting::whereIn('meta_key', $metaKeys)->get()->keyBy('meta_key');

        return view('admin.generalSettings.fees.FinancialSettingsForm', [
            'feesetup_guest_service_charge' => $settings['feesetup_guest_service_charge'] ?? null,
            'feesetup_iva_tax' => $settings['feesetup_iva_tax'] ?? null,
            'feesetup_accomodation_tax' => $settings['feesetup_accomodation_tax'] ?? null,
            'feesetup_admin_commission' => $settings['feesetup_admin_commission'] ?? null,
            'feesetup_accomodation_tax_get' => $settings['feesetup_accomodation_tax_get'] ?? null,
            'feesetup_iva_tax_get' => $settings['feesetup_iva_tax_get'] ?? null,
            'feesetup_guest_service_charge_get' => $settings['feesetup_guest_service_charge_get'] ?? null,
        ]);
    }

    public function FeesSetupAdd(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return redirect()->back()->with('error', 'Form submission is disabled in demo mode.');
        }
        $formData = $request->except('_token');
        foreach ($formData as $metaKey => $metaValue) {
            if ($metaValue !== null) {
                GeneralSetting::updateOrCreate(
                    ['meta_key' => $metaKey],
                    ['meta_value' => $metaValue]
                );
            }
        }

        return redirect()->route('admin.fees')->with('success', 'Updated successfully.');
        // return redirect()->route('admin.fees');
    }

    public function language()
    {
        $listdata = Language::all();

        return view('admin.language.language_form', compact('listdata'));
    }

    public function apiInformations()
    {
        $meta_keys = [
            'api_facebook_client_id',
            'api_facebook_client_secret',
            'api_google_client_id',
            'api_google_client_secret',
            'api_google_map_key',
            'general_captcha',
            'site_key',
            'private_key',
        ];
        $settings = GeneralSetting::whereIn('meta_key', $meta_keys)->get()->keyBy('meta_key');
        $data = [];
        foreach ($meta_keys as $key) {
            $data[$key] = $settings->has($key) ? $settings->get($key) : '';
        }

        return view('admin.generalSettings.apicredentials.apikeymanagementform', $data);
    }

    public function apiAuthenticationAdd(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return redirect()->back()->with('error', 'Form submission is disabled in demo mode.');
        }
        $formData = $request->except('_token');
        foreach ($formData as $metaKey => $metaValue) {
            if (! empty($metaValue)) {
                GeneralSetting::updateOrCreate(
                    ['meta_key' => $metaKey],
                    ['meta_value' => $metaValue]
                );
            }
        }

        return redirect()->route('admin.api-informations')->with('success', 'Updated successfully.');
        // return redirect()->route('admin.api-informations');
    }

    public function addLanguage()
    {
        return view('admin.language.create');
    }

    public function addLanguageData(Request $request)
    {
        $data = [
            'name' => $request->name,
            'short_name' => $request->short_name,
            'language_status' => $request->language_status,
        ];
        Language::create($data);

        return redirect()->route('admin.language');
    }

    public function editLanguage(Request $request)
    {
        $id = $request->id;
        $editdata = Language::find($id);

        return view('admin.language.edit', compact('editdata'));
    }

    public function editLanguageData(Request $request)
    {
        $id = $request->id;
        Language::where('id', $id)->update([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'language_status' => $request->language_status,
        ]);

        return redirect()->route('admin.language');
    }

    public function deleteLanguage($id)
    {
        Language::where('id', $id)->delete();

        return response()->json(['message' => 'Language deleted successfully.']);
    }

    // public function updateStatus(Request $request)
    // {
    //     if (Gate::denies('general_setting_edit')) {
    //         return response()->json(['error' => 'Form submission is disabled in demo mode.']);
    //     }
    //     $status = $request->input('status');
    //     $id = $request->input('id');
    //     $setting = GeneralSetting::where('meta_key', 'paypal_status')->first();
    //     if ($setting) {
    //         $setting->meta_value = $status;
    //         $setting->save();
    //     }
    //     return response()->json(['success' => "Updated Successfully."]);
    // }
    // public function updateMethodsStatus(Request $request)
    // {
    //     if (Gate::denies('general_setting_edit')) {
    //         return redirect()->back()->with('error', 'Form submission is disabled in demo mode.');
    //     }
    //     $status = $request->input('status');
    //     $id = $request->input('id');
    //     $setting = GeneralSetting::where('meta_key', 'paydunya_status')->first();
    //     if ($setting) {
    //         $setting->meta_value = $status;
    //         $setting->save();
    //     }
    //     return response()->json(['success' => true]);
    // }
    public function updateNonageStatus(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return redirect()->back()->with('error', 'Form submission is disabled in demo mode.');
        }
        error_log('Update Nonage Status called'); // Debug log
        $status = $request->input('status');
        $id = $request->input('id');
        error_log('Status: '.$status.', ID: '.$id); // Log received status and id
        // Update Nonage status
        $nonageSetting = GeneralSetting::firstOrNew(['meta_key' => 'nonage_status']);
        $nonageSetting->meta_value = $status;
        $nonageSetting->save();
        // If Nonage is activated, deactivate Twillio
        if ($status === 'Active') {
            $twillioSetting = GeneralSetting::where('meta_key', 'twillio_status')->first();
            if ($twillioSetting) {
                $twillioSetting->meta_value = 'Inactive';
                $twillioSetting->save();
            }
        }

        return response()->json(['success' => true]);
    }

    public function updateTwillioeStatus(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return redirect()->back()->with('error', 'Form submission is disabled in demo mode.');
        }
        error_log('Update Twillio Status called'); // Debug log
        $status = $request->input('status');
        $id = $request->input('id');
        error_log('Status: '.$status.', ID: '.$id); // Log received status and id
        // Update Twillio status
        $twillioSetting = GeneralSetting::firstOrNew(['meta_key' => 'twillio_status']);
        $twillioSetting->meta_value = $status;
        $twillioSetting->save();
        // If Twillio is activated, deactivate Nonage
        if ($status === 'Active') {
            $nonageSetting = GeneralSetting::where('meta_key', 'nonage_status')->first();
            if ($nonageSetting) {
                $nonageSetting->meta_value = 'Inactive';
                $nonageSetting->save();
            }
        }

        return response()->json(['success' => true]);
    }

    public function updateSMSProviderName(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return redirect()->back()->with('error', 'Form submission is disabled in demo mode.');
        }
        error_log('Update Sinch Status called'); // Debug log
        $status = $request->input('status');
        $id = $request->input('id');
        $userValue = $request->input('userValue');
        error_log('Status: '.$status.', ID: '.$id); // Log received status and id
        // Update sinch status
        $smsSetting = GeneralSetting::firstOrNew(['meta_key' => 'sms_provider_name']);
        $smsSetting->meta_value = $userValue;
        $smsSetting->save();

        return response()->json(['success' => true]);
    }

    // GeneralSettingController.php
    public function pushNotificaTionSetting()
    {
        $settings = GeneralSetting::whereIn('meta_key', [
            'push_notification_status',
            'firebase_project_id',
            'firebase_service_account_json_path',
        ])->pluck('meta_value', 'meta_key');
        $pushnotification_status = $settings['push_notification_status'] ?? null;
        $firebase_project_id = $settings['firebase_project_id'] ?? null;
        $firebase_service_account_json_path = $settings['firebase_service_account_json_path'] ?? null;
        $userids = AppUser::where('user_type', 'user')->where('status', 1)->get()->mapWithKeys(function ($user) {
            return [$user->id => $user->first_name.' - '.$user->phone.' - '.$user->email];
        })->prepend(trans('global.pleaseSelect'), '');
        $drivers = AppUser::where('user_type', 'driver')->where('status', 1)->where('document_verify', 1)->get()->mapWithKeys(function ($user) {
            return [$user->id => $user->first_name.' - '.$user->phone.' - '.$user->email];
        })->prepend(trans('global.pleaseSelect'), '');

        return view('admin.generalSettings.pushnotification.pushnotification', compact(
            'userids',
            'drivers',
            'pushnotification_status',
            'firebase_project_id',
            'firebase_service_account_json_path'
        ));
    }

    public function pushNotificationUpdate(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return response()->json(['error' => 'Form submission is disabled in demo mode.'], 403);
        }
        $request->validate([
            'firebase_project_id' => 'required|string',
            'firebase_service_account_json_path' => 'nullable|string',
        ]);

        GeneralSetting::updateOrCreate(
            ['meta_key' => 'firebase_project_id'],
            ['meta_value' => $request->firebase_project_id]
        );

        $serviceAccountPath = trim((string) $request->input('firebase_service_account_json_path', ''));
        if ($serviceAccountPath === '') {
            $serviceAccountPath = 'firebase/service_account.json';
        }
        GeneralSetting::updateOrCreate(
            ['meta_key' => 'firebase_service_account_json_path'],
            ['meta_value' => $serviceAccountPath]
        );

        GeneralSetting::updateOrCreate(
            ['meta_key' => 'push_notification_status'],
            ['meta_value' => 'firebase']
        );

        return response()->json(['success' => 'Firebase notification settings updated successfully!']);
    }

    public function sendUserMessage(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return response()->json(['error' => 'Form submission is disabled in demo mode.'], 403);
        }
        $request->validate([
            'userid_id' => 'required',
            'message' => 'required|string',
        ]);
        $message = $request->message ?? '';
        $subject = $request->subject ?? '';
        if ($request->userid_id == 'All') {
            $users = AppUser::with('metadata')->where('user_type', $request->user_type)->get();
        } else {
            $users = AppUser::with('metadata')->where('id', $request->userid_id)->where('user_type', $request->user_type)->get();
        }
        foreach ($users as $user) {
            if (! empty($user['fcm'])) {
                $this->sendPushNotification($user['fcm'], $subject, $message, $user->user_type);
            }
        }

        return response()->json(['success' => 'Notification sent successfully!']);
    }

    private function sendPushNotification($fcm, $subject, $message, $userType = null)
    {
        if (Gate::denies('general_setting_edit')) {
            return response()->json(['error' => 'Form submission is disabled in demo mode.']);
        }
        $this->sendFcmMessage($fcm, $subject, $message, [], 0, $userType);
    }

    public function updatePushNotificationStatus(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return redirect()->back()->with('error', 'Form submission is disabled in demo mode.');
        }
        $type = $request->input('type');
        $pushNotificationStatus = GeneralSetting::firstOrNew(['meta_key' => 'push_notification_status']);
        $pushNotificationStatus->meta_value = $type === 'firebase' ? 'firebase' : 'firebase';
        $pushNotificationStatus->save();

        return response()->json(['success' => true]);
    }

    public function currencySetting()
    {
        $keys = [
            'currency_auth_key',
            'general_default_currency',
            'multicurrency_status',
        ];
        $settings = GeneralSetting::whereIn('meta_key', $keys)->get()->keyBy('meta_key');
        $general_default_currency = $settings->get('general_default_currency') ?? null;
        $currency_auth_key = $settings->get('currency_auth_key') ?? null;
        $multicurrency_status = $settings->get('multicurrency_status') ?? null;
        $allcurrency = Currency::where('status', 1)->get();
        $id = 1;

        return view('admin.generalSettings.currencysettings.currency', compact('currency_auth_key', 'general_default_currency', 'allcurrency', 'multicurrency_status'));
    }

    public function updateCurrencyAuthKey(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return redirect()->back()->with('error', 'Form submission is disabled in demo mode.');
        }
        $formData = $request->except('_token');
        // print_r($formData);
        // exit;
        foreach ($formData as $metaKey => $metaValue) {
            if (! empty($metaValue)) {
                GeneralSetting::updateOrCreate(
                    ['meta_key' => $metaKey],
                    ['meta_value' => $metaValue]
                );
            }
        }

        return redirect()->route('admin.currencySetting')->with('success', 'Updated successfully.');
    }

    public function updateAutoFillOTP(Request $request)
    {
        if (Gate::denies('general_setting_edit')) {
            return response()->json(['error' => 'Form submission is disabled in demo mode.'], 403);
        }
        $status = $request->input('status');
        if ($status == 'Active') {
            $stat = 1;
        } else {
            $stat = 0;
        }
        $id = $request->input('id');
        // Using updateOrCreate to update or insert the setting
        $setting = GeneralSetting::updateOrCreate(
            ['meta_key' => 'auto_fill_otp'],
            ['meta_value' => $stat]
        );

        return response()->json(['success' => true]);
    }

    public function setMulticurrency(Request $request)
    {
        $stat = $request->input('status') === 'Active' ? 1 : 0;
        $id = $request->input('id');
        $setting = GeneralSetting::updateOrCreate(
            ['meta_key' => 'multicurrency_status'],
            ['meta_value' => $stat]
        );

        return response()->json(['success' => true]);
    }

    public function projectSetup()
    {
        return view('admin.generalSettings.project-setup.index');
    }

    public function projectSetupUpdate(Request $request)
    {

        abort_if(Gate::denies('general_setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            function deleteDirectory($dirPath)
            {
                if (! is_dir($dirPath)) {
                    return;
                }
                $files = scandir($dirPath);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    $filePath = $dirPath.DIRECTORY_SEPARATOR.$file;
                    is_dir($filePath) ? deleteDirectory($filePath) : unlink($filePath);
                }
                rmdir($dirPath);
            }
            $publicStoragePath = public_path('storage');
            if (is_link($publicStoragePath)) {
                unlink($publicStoragePath);  // If it's a symlink, remove it
            } elseif (is_dir($publicStoragePath)) {
                deleteDirectory($publicStoragePath);  // If it's a directory, delete recursively
            }
            // Create storage link if not exists
            if (! file_exists(public_path('storage'))) {
                Artisan::call('storage:link');
            }

            return response()->json([
                'success' => true,
                'message' => 'Project setup completed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project setup failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function projectCleanupUpdate(Request $request)
    {

        return;
        abort_if(Gate::denies('general_setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        Media::whereIn('model_type', ['App\Models\AppUser', 'App\Models\Modern\Item'])->forceDelete();
        Item::query()->forceDelete();
        Booking::query()->forceDelete();
        AppUser::query()->forceDelete();
        Review::query()->forceDelete();
        AddCoupon::query()->forceDelete();
        VehicleOdometer::query()->forceDelete();
        ItemType::query()->forceDelete();
        ItemFeatures::query()->forceDelete();
        VehicleMake::query()->forceDelete();
        City::query()->forceDelete();
        RentalItemRule::query()->forceDelete();
        PersonalAccessToken::query()->forceDelete();

        // to restart auto increment from 1
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $tables = [
            'media',
            'rental_item_wishlists',
            'rental_item_meta',
            'rental_items',
            'wallets',
            'payouts',
            'vendor_wallets',
            'transactions',
            'reviews',
            'bookings',
            'add_coupons',
            'app_user_otps',
            'app_user_meta',
            'app_users',
        ];
        foreach ($tables as $table) {
            DB::statement("TRUNCATE TABLE {$table};");
        }
        $settings = [
            'auto_fill_otp' => 1,
            'general_captcha' => 'no',
            'onlinepayment' => 'Inactive',
            'api_google_map_key' => 'test',
            'site_key' => 'test',
            'private_key' => 'test',
            'messagewizard_key' => 'test',
            'messagewizard_secret' => 'test',
            'messagewizard_sender_number' => 'test',
            'twillio_number' => 'test',
            'twillio_key' => 'test',
            'twillio_secret' => 'test',
            'sinch_service_plan_id' => 'test',
            'sinch_api_token' => 'test',
            'sinch_sender_number' => 'test',
            'msg91_auth_key' => 'test',
            'msg91_template_id' => 'test',
            'host' => 'test',
            'port' => '111',
            'username' => 'test',
            'password' => 'test',
            'encryption' => 'test',
            'from_email' => 'test',
            'currency_auth_key' => 'test',
            'firebase_project_id' => 'test-project-id',
            'firebase_service_account_json_path' => '',
            'test_paypal_client_id' => 'test',
            'test_paypal_secret_key' => 'test',
            'live_paypal_client_id' => 'test',
            'live_paypal_secret_key' => 'test',
            'test_stripe_public_key' => 'test',
            'test_stripe_secret_key' => 'test',
            'live_stripe_public_key' => 'test',
            'live_stripe_secret_key' => 'test',
        ];
        foreach ($settings as $meta_key => $meta_value) {
            GeneralSetting::updateOrCreate(
                ['meta_key' => $meta_key],
                ['meta_value' => $meta_value]
            );
        }
        $storagePath = storage_path('app/public');
        $excludedFolder = 'logo';

        $allFolders = File::directories($storagePath);
        foreach ($allFolders as $folder) {
            if (basename($folder) !== $excludedFolder) {
                File::deleteDirectory($folder);
            }
        }

        $allFiles = File::files($storagePath);
        foreach ($allFiles as $file) {
            File::delete($file);
        }

        $uploadPath = storage_path('tmp/uploads');
        if (File::exists($uploadPath)) {
            File::cleanDirectory($uploadPath);
        }
        $mediaTempPath = storage_path('media-library/temp');
        if (File::exists($mediaTempPath)) {
            File::cleanDirectory($mediaTempPath);
        }
        $logsPath = storage_path('logs');
        if (File::exists($logsPath)) {
            File::cleanDirectory($logsPath);
        }

        $sessionsPath = storage_path('framework/sessions');
        if (File::exists($sessionsPath)) {
            $files = File::files($sessionsPath);
            foreach ($files as $file) {
                @unlink($file);
            }
        }

        $viewsPath = storage_path('framework/views');
        if (File::exists($viewsPath)) {
            $files = File::files($viewsPath);
            foreach ($files as $file) {
                @unlink($file);
            }
        }

        $testingPath = storage_path('framework/testing');
        if (File::exists($testingPath)) {
            $files = File::files($testingPath);
            foreach ($files as $file) {
                @unlink($file);
            }
        }

        $debugbarPath = storage_path('debugbar');

        if (File::exists($debugbarPath)) {
            File::cleanDirectory($debugbarPath);
        }

        $phpstanCachePath = base_path('tmp/phpstan/cache');

        if (File::exists($phpstanCachePath)) {
            File::cleanDirectory($phpstanCachePath);
        }

        $requiredDirs = [
            storage_path('app'),
            storage_path('app/public'),
            storage_path('framework'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('framework/testing'),
            storage_path('logs'),
            storage_path('debugbar'),

        ];

        foreach ($requiredDirs as $dir) {
            if (! File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $indexFile = $dir.DIRECTORY_SEPARATOR.'index.php';
            $gitignoreFile = $dir.DIRECTORY_SEPARATOR.'.gitignore';

            if (! file_exists($indexFile)) {
                file_put_contents($indexFile, "<?php\n// Silence is golden.\n");
            }

            if (! file_exists($gitignoreFile)) {
                file_put_contents($gitignoreFile, "*\n!.gitignore\n!index.php\n");
            }
        }

        $filePathLoginCred = resource_path('views/admin/demo/demo-user.blade.php'); // Adjust the path if needed
        file_put_contents($filePathLoginCred, '');
        $filePathwhatsapp = resource_path('views/admin/demo/whatsapp-chat.blade.php'); // Adjust the path if needed
        file_put_contents($filePathwhatsapp, '');

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('config:cache');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('event:clear');
        Artisan::call('optimize:clear');

        return response()->json([
            'success' => true,
            'message' => 'Project cleanup completed successfully',
        ]);

        try {
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project cleanup failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function showAppSettings()
    {
        $firebase_update_interval = GeneralSetting::where('meta_key', 'firebase_update_interval')->value('meta_value');
        $location_accuracy_threshold = GeneralSetting::where('meta_key', 'location_accuracy_threshold')->value('meta_value');
        $background_location_interval = GeneralSetting::where('meta_key', 'background_location_interval')->value('meta_value');
        $driver_search_interval = GeneralSetting::where('meta_key', 'driver_search_interval')->value('meta_value');
        $use_google_after_pickup = GeneralSetting::where('meta_key', 'use_google_after_pickup')->value('meta_value');
        $use_google_before_pickup = GeneralSetting::where('meta_key', 'use_google_before_pickup')->value('meta_value');
        $minimum_hits_time = GeneralSetting::where('meta_key', 'minimum_hits_time')->value('meta_value');
        $use_google_source_destination = GeneralSetting::where('meta_key', 'use_google_source_destination')->value('meta_value');

        return view('admin.generalSettings.app-settings.index', compact(
            'firebase_update_interval',
            'location_accuracy_threshold',
            'background_location_interval',
            'driver_search_interval',
            'use_google_after_pickup',
            'use_google_before_pickup',
            'minimum_hits_time',
            'use_google_source_destination'
        ));
    }

    public function updateAppSettings(Request $request)
    {

        abort_if(Gate::denies('general_setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'firebase_update_interval' => 'required|integer|min:1',
            'location_accuracy_threshold' => 'nullable|numeric|min:0',
            'background_location_interval' => 'nullable|integer|min:1',
            'driver_search_interval' => 'nullable|integer|min:1',
            'use_google_after_pickup' => 'nullable|in:0,1',
            'use_google_before_pickup' => 'nullable|in:0,1',
            'minimum_hits_time' => 'nullable|integer|min:1',
            'use_google_source_destination' => 'nullable|in:0,1',
        ]);
        $settings = [
            'firebase_update_interval' => $request->firebase_update_interval,
            'location_accuracy_threshold' => $request->location_accuracy_threshold,
            'background_location_interval' => $request->background_location_interval,
            'driver_search_interval' => $request->driver_search_interval,
            'use_google_after_pickup' => $request->use_google_after_pickup,
            'use_google_before_pickup' => $request->use_google_before_pickup,
            'minimum_hits_time' => $request->minimum_hits_time,
            'use_google_source_destination' => $request->use_google_source_destination,
        ];
        foreach ($settings as $key => $value) {
            GeneralSetting::updateOrCreate(['meta_key' => $key], ['meta_value' => $value]);
        }

        return response()->json(['success' => 'App settings updated successfully.']);
    }
}
