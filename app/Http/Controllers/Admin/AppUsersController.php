<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FirestoreTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\NotificationTrait;
use App\Http\Controllers\Traits\UserWalletTrait;
use App\Http\Controllers\Traits\VendorWalletTrait;
use App\Http\Requests\StoreAppUserRequest;
use App\Http\Requests\UpdateAppUserRequest;
use App\Models\AllPackage;
use App\Models\AppUser;
use App\Models\AppUserMeta;
use App\Models\GeneralSetting;
use App\Models\Modern\ItemType;
use App\Models\PayoutMethod;
use App\Services\FirebaseAuthService;
use Gate;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class AppUsersController extends Controller
{
    use FirestoreTrait, MediaUploadingTrait, NotificationTrait, UserWalletTrait, VendorWalletTrait;

    public function index()
    {
        abort_if(Gate::denies('app_user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from = request('from');
        $to = request('to');
        $status = request('status');
        $customer = request('customer');
        $hostStatus = request('host_status');
        $userType = 'user';
        $query = AppUser::with(['media']) // limit fields if possible
            ->where('user_type', $userType)
            ->orderByDesc('id');
        if ($from && $to) {
            $query->whereBetween('created_at', [
                date('Y-m-d', strtotime($from)).' 00:00:00',
                date('Y-m-d', strtotime($to)).' 23:59:59',
            ]);
        } elseif ($from) {
            $query->where('created_at', '>=', date('Y-m-d', strtotime($from)).' 00:00:00');
        } elseif ($to) {
            $query->where('created_at', '<=', date('Y-m-d', strtotime($to)).' 23:59:59');
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($hostStatus !== null) {
            $query->where('host_status', $hostStatus);
        }

        if ($customer) {
            $query->where('id', $customer);
        }

        $appUsers = $query->paginate(50)->appends(request()->only(['from', 'to', 'status', 'customer', 'host_status']));
        $selectedUser = $customer ? $appUsers->firstWhere('id', $customer) : null;
        $searchfield = $selectedUser ? "{$selectedUser->first_name} {$selectedUser->last_name} ({$selectedUser->phone})" : 'All';
        $searchfieldId = $selectedUser->id ?? '';
        $statusCounts = AppUser::selectRaw('
            COUNT(*) as live,
            SUM(status = 1) as active,
            SUM(status = 0) as inactive,
            SUM(host_status = 2) as requested
        ')

            ->where('user_type', $userType)
            ->first()
            ->toArray();

        $statusCounts['trash'] = '';

        return view('admin.appUsers.index', compact('appUsers', 'statusCounts', 'searchfield', 'searchfieldId'));
    }

    public function create()
    {
        abort_if(Gate::denies('app_user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $packages = AllPackage::pluck('package_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.appUsers.create', compact('packages'));
    }

    public function store(StoreAppUserRequest $request)
    {
        $userEmail = AppUser::where('email', $request->email)->first();
        if ($userEmail) {
            return redirect()->to('admin/app-users/create')->withErrors(['email' => 'Email already exists.']);
        }
        $userPhone = AppUser::where('phone', $request->phone)->first();
        if ($userPhone) {
            return redirect()->to('admin/app-users/create')->withErrors(['phone' => 'Phone number already exists.']);
        }

        // $appUser = AppUser::create($request->all());
        $data = $request->all();
        if ($request->input('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $appUser = AppUser::create($data);

        if ($request->input('profile_image', false)) {
            $appUser->addMedia(storage_path('tmp/uploads/'.basename($request->input('profile_image'))))->toMediaCollection('profile_image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $appUser->id]);
        }

        return redirect()->route('admin.app-users.index');
    }

    public function edit(AppUser $appUser)
    {
        abort_if(Gate::denies('app_user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $packages = AllPackage::pluck('package_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $appUser->load('package');

        return view('admin.appUsers.edit', compact('appUser', 'packages'));
    }

    public function update_bk(UpdateAppUserRequest $request, AppUser $appUser)
    {
        $userEmail = AppUser::where('email', $request->email)->first();

        if ($userEmail && $userEmail->id !== $appUser->id) {
            return redirect()->to("admin/app-users/{$appUser->id}/edit?host_status=".request()->input('host_status'))
                ->withErrors(['email' => 'Email already exists.']);
        }

        $userPhone = AppUser::where('phone', $request->phone)->first();
        if ($userPhone && $userPhone->id !== $appUser->id) {
            return redirect()->to("admin/app-users/{$appUser->id}/edit?host_status=".request()->input('host_status'))
                ->withErrors(['phone' => 'Phone number already exists.']);
        }
        $data = $request->except(['password', 'host_status']);

        if (! empty($request->input('password'))) {
            $data['password'] = Hash::make($request->input('password'));
        }
        $appUser->update($data);

        // Handle profile image
        if ($request->input('profile_image', false)) {
            if (! $appUser->profile_image || $request->input('profile_image') !== $appUser->profile_image->file_name) {
                if ($appUser->profile_image) {
                    $appUser->profile_image->delete();
                }
                $appUser->addMedia(storage_path('tmp/uploads/'.basename($request->input('profile_image'))))
                    ->toMediaCollection('profile_image');
            }
        } elseif ($appUser->profile_image) {
            $appUser->profile_image->delete();
        }

        // ✅ Redirect with host_status to keep the correct tab active
        return redirect()->route(
            'admin.app-users.index',
            request()->input('host_status') == '0' ? ['host_status' => '0'] : []
        );
    }

    public function show(AppUser $appUser)
    {
        $userId = $appUser->id;
        if (! is_numeric($userId)) {
            abort(404, 'Invalid user ID');
        }
        $general_default_currency = View::shared('general_default_currency');
        $statusCounts = $appUser->bookings()
            ->selectRaw("
            COUNT(*) as total,
            SUM(status = 'ongoing') as live,
            SUM(status = 'cancelled') as cancelled,
            SUM(status = 'rejected') as rejected,
            SUM(status = 'completed') as completed
        ")
            ->first();
        $data = [
            'live_rides' => (int) ($statusCounts->live ?? 0),
            'cancelled_rides' => (int) ($statusCounts->cancelled ?? 0),
            'rejected_rides' => (int) ($statusCounts->rejected ?? 0),
            'completed_rides' => (int) ($statusCounts->completed ?? 0),
            'total_rides' => (int) ($statusCounts->total ?? 0),
        ];

        return view('admin.appUsers.rider.profile', compact('appUser', 'data', 'userId', 'general_default_currency'));
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('app_user_create') && Gate::denies('app_user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model = new AppUser;
        $model->id = $request->input('crud_id', 0);
        $model->exists = true;
        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
    //

    public function customerSearch(Request $request)
    {

        $searchTerm = $request->input('q');
        $customers = AppUser::where('host_status', '0')
            ->where('user_type', 'user')
            ->where(function ($query) use ($searchTerm) {
                $query->where('first_name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('last_name', 'like', '%'.$searchTerm.'%');
            })
            ->get();

        $data = [];
        foreach ($customers as $customer) {

            $name = $customer->first_name.' '.$customer->last_name.'('.$customer->phone.')';
            $data[] = [
                'id' => $customer->id,
                'name' => $customer->first_name,
                'first_name' => $name,
            ];
        }

        return response()->json($data);
    }

    public function typeSearch(Request $request)
    {
        $searchTerm = $request->input('q', '');
        $page = (int) $request->input('page', 1);
        $perPage = 10;

        $query = ItemType::where('name', 'like', "%{$searchTerm}%");

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $results = $paginator->items();
        $data = array_map(function ($type) {
            return [
                'id' => $type->id,
                'text' => $type->name,
            ];
        }, $results);

        return response()->json([
            'results' => $data,
            'pagination' => [
                'more' => $paginator->hasMorePages(),
            ],
        ]);
    }

    public function hostSearch(Request $request)
    {
        // Get the search term entered by the user
        $searchTerm = $request->input('q');
        $customers = AppUser::where('user_type', 'driver')
            ->where(function ($query) use ($searchTerm) {
                $query->where('first_name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('last_name', 'like', '%'.$searchTerm.'%');
            })
            ->get();

        $data = [];
        foreach ($customers as $customer) {
            $name = $customer->first_name.' '.$customer->last_name.'('.$customer->phone.')';
            $data[] = [
                'id' => $customer->id,
                'name' => $customer->first_name,
                'first_name' => $name,
            ];
        }

        return response()->json($data);
    }

    public function userSearch(Request $request)
    {

        $searchTerm = $request->input('q');
        $customers = AppUser::where('host_status', '0')
            ->where('user_type', 'user')
            ->where(function ($query) use ($searchTerm) {
                $query->where('first_name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('last_name', 'like', '%'.$searchTerm.'%');
            })
            ->get();

        $data = [];
        foreach ($customers as $customer) {

            $name = $customer->first_name.' '.$customer->last_name.'('.$customer->phone.')';
            $data[] = [
                'id' => $customer->id,
                'name' => $customer->first_name,
                'first_name' => $name,
            ];
        }

        return response()->json($data);
    }

    public function updateStatus(Request $request)
    {

        if (Gate::denies('app_user_edit')) {
            return response()->json([
                'status' => 403,
                'message' => "You don't have permission to perform this action.",
            ], 403);
        }
        $statusData = AppUser::where('id', $request->pid)->update(['status' => $request->status]);
        if ($statusData) {
            return response()->json([
                'status' => 200,
                'message' => trans('global.status_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }
    }

    public function updateHostStatus(Request $request)
    {
        if (Gate::denies('app_user_edit')) {
            return response()->json([
                'status' => 403,
                'message' => "You don't have permission to perform this action.",
            ], 403);
        }
        $statusData = AppUser::where('id', $request->pid)->update(['host_status' => $request->status]);
        if ($statusData) {
            $user = AppUser::where('id', $request->pid)->first();

            if ($user->host_status) {
                $template_id = 35;
                $valuesArray = $user->toArray();
                $valuesArray = $user->only(['first_name', 'last_name', 'email']);
                $settings = GeneralSetting::whereIn('meta_key', ['general_email'])->get()->keyBy('meta_key');
                $general_email = $settings['general_email']->meta_value ?? null;
                $valuesArray['support_email'] = $general_email;
                $valuesArray['phone'] = $user->phone_country.$user->phone;
                $this->sendAllNotifications($valuesArray, $request->pid, $template_id);
            }

            $user->items()->update(['status' => $request->status]);

            return response()->json([
                'status' => 200,
                'message' => trans('global.status_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }
    }

    public function updateIdentifyGrpc(Request $request, FirebaseAuthService $firebaseAuthService)
    {
        $verified = AppUser::where('id', $request->pid)->update(['document_verify' => $request->verified]);
        $user = AppUser::find($request->pid);

        $documentKeys = [
            'driving_licence_front_status',
            'driving_licence_back_status',
            'driver_id_front_status',
            'driver_id_back_status',
        ];

        if ($request->verified == 1) {
            foreach ($documentKeys as $key) {
                $user->metadata()->updateOrCreate(
                    ['meta_key' => $key],
                    ['meta_value' => 'approved']
                );
            }
        }

        if ($verified) {
            $firestoreDocId = $user->firestore_id;
            $firestoreDocExists = false;

            if ($firestoreDocId) {
                $docData = $this->getDocument('drivers', $firestoreDocId);
                $firestoreDocExists = $docData !== null;
            }
            if (! $firestoreDocId || ! $firestoreDocExists) {
                $firestoreData = $this->generateDriverFirestoreData($user);
                $firestoreDoc = $this->storeDriverInFirestore($firestoreData);
                $firestoreDocId = $firestoreDoc->id();
                $user->update(['firestore_id' => $firestoreDocId]);
                $user['firestore_id'] = $firestoreDocId;
            }

            $user->update(['host_status' => 1]);

            $this->updateDocument('drivers', $firestoreDocId, [
                'docApprovedStatus' => $request->verified ? 'approved' : 'rejected',
                'driverId' => $request->pid,
            ]);

            return response()->json([
                'status' => 200,
                'message' => trans('global.identify_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong. Please try again.',
            ]);
        }
    }

    public function updateIdentify(Request $request, FirebaseAuthService $firebaseAuthService)
    {
        $verified = AppUser::where('id', $request->pid)->update(['document_verify' => $request->verified]);
        $user = AppUser::find($request->pid);

        $documentKeys = [
            'driving_licence_front_status',
            'driving_licence_back_status',
            'driver_id_front_status',
            'driver_id_back_status',
        ];

        if ($request->verified == 1) {
            foreach ($documentKeys as $key) {
                $user->metadata()->updateOrCreate(
                    ['meta_key' => $key],
                    ['meta_value' => 'approved']
                );
            }
        }

        if ($verified) {
            $firestoreDocId = $user->firestore_id;
            $firestoreDocExists = false;

            if ($firestoreDocId) {
                $docData = $this->getDocument('drivers', $firestoreDocId);
                $firestoreDocExists = $docData !== null;
            }
            if (! $firestoreDocId || ! $firestoreDocExists) {
                $firestoreData = $this->generateDriverFirestoreData($user);
                $firestoreDoc = $this->storeDriverInFirestore($firestoreData);
                $firestoreDocId = basename($firestoreDoc);
                $user->update(['firestore_id' => $firestoreDocId]);
                $user['firestore_id'] = $firestoreDocId;
            }

            $user->update(['host_status' => 1]);

            $this->updateDocument('drivers', $firestoreDocId, [
                'docApprovedStatus' => $request->verified ? 'approved' : 'rejected',
                'driverId' => $request->pid,
            ]);

            return response()->json([
                'status' => 200,
                'message' => trans('global.identify_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong. Please try again.',
            ]);
        }
    }

    public function updatePhoneverify(Request $request)
    {

        $phoneVerify = AppUser::where('id', $request->pid)->update(['phone_verify' => $request->phone_verify]);
        if ($phoneVerify) {
            return response()->json([
                'ststus' => 200,
                'message' => trans('global.phone_verify_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'ststus' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }
    }

    public function updateEmailverify(Request $request)
    {

        $emailVerify = AppUser::where('id', $request->pid)->update(['email_verify' => $request->email_verify]);
        if ($emailVerify) {
            return response()->json([
                'ststus' => 200,
                'message' => trans('global.email_verify_updated_successfully'),
            ]);
        } else {
            return response()->json([
                'ststus' => 500,
                'message' => 'something went wrong. Please try again.',
            ]);
        }
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('app_user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appUser = AppUser::findOrFail($id);

        if ($appUser) {

            if ($appUser->firestore_id) {
                $this->deleteFirestoreDriver($appUser->firestore_id);
            }
            $appUser->forceDelete();

            return response()->json([
                'status' => 'success',
                'message' => 'appUser deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: appUser cannot be deleted from this module',
            ], Response::HTTP_FORBIDDEN);
        }
    }

    // App Users data Trash

    public function appUserTrashed()
    {
        abort_if(Gate::denies('app_user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from = request()->input('from');
        $to = request()->input('to');
        $status = request()->input('status');
        $customer = request()->input('customer');

        $query = AppUser::onlyTrashed()->with(['package', 'media'])->orderBy('id', 'desc');

        $isFiltered = ($from || $to || $status || $customer);

        if ($from && $to) {
            $query->whereBetween('created_at', [date('Y-m-d', strtotime($from)).' 00:00:00', date('Y-m-d', strtotime($to)).' 23:59:59']);
        } elseif ($from) {
            $query->where('created_at', '>=', date('Y-m-d', strtotime($from)).' 00:00:00');
        } elseif ($to) {
            $query->where('created_at', '<=', date('Y-m-d', strtotime($to)).' 23:59:59');
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($customer) {
            $query->where('id', $customer);
        }

        $appUsers = $isFiltered ? $query->paginate(50) : $query->paginate(50);

        $queryParameters = [];

        if ($from != null) {
            $queryParameters['from'] = $from;
        }
        if ($to != null) {
            $queryParameters['to'] = $to;
        }
        if ($status != null) {
            $queryParameters['status'] = $status;
        }
        if ($customer != null) {
            $queryParameters['customer'] = $customer;
        }
        if ($customer != null && $status != '' && $from != '' && $to != '') {
            $queryParameters['customer'] = $customer;
            $queryParameters['status'] = $status;
            $queryParameters['to'] = $to;
            $queryParameters['from'] = $from;
        }
        $appUsers->appends($queryParameters);

        $fielddata = request()->input('customer');
        $fieldname = AppUser::find($fielddata);

        if ($fieldname) {

            $searchfield = $fieldname->first_name.' '.$fieldname->last_name.'('.$fieldname->phone.')';
        } else {

            $searchfield = 'All';
        }

        $statusCounts = [
            'live' => AppUser::count(),
            'active' => AppUser::where('status', 1)->count(),
            'inactive' => AppUser::where('status', 0)->count(),
            'mail' => AppUser::whereNotNull('email')->count(),
            'trash' => AppUser::onlyTrashed('trash')->count(),
        ];

        return view('admin.appUsers.trash', compact('appUsers', 'statusCounts', 'searchfield'));
    }

    public function restoreTrash($id)
    {
        $item = AppUser::onlyTrashed()->findOrFail($id);
        $item->restore();
    }

    public function permanentDelete($id)
    {
        $appUser = AppUser::onlyTrashed()->findOrFail($id);
        if ($appUser->profile_image) {
            $appUser->profile_image->delete();
            $appUser->clearMediaCollection('profile_image');
        }
        $appUser->forceDelete();

        return response()->json(['message' => 'User permanently deleted successfully.']);
    }

    public function permanentDeleteAll(Request $request)
    {

        $trashedUsers = AppUser::onlyTrashed()->limit(5)->get();

        if ($trashedUsers->isEmpty()) {
            return response()->json(['message' => trans('global.no_items_to_delete')], 404);
        }

        foreach ($trashedUsers as $user) {

            if ($user->profile_image) {
                $user->profile_image->delete();
                $user->clearMediaCollection('profile_image');
            }

            $user->forceDelete();
        }

        return response()->json(['message' => trans('global.all_permanently_deleted_success')]);
    }

    public function deleteAll(Request $request)
    {
        abort_if(Gate::denies('app_user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ids = $request->input('ids');
        if (! empty($ids)) {
            try {

                AppUser::whereIn('id', $ids)->forceDelete();

                return response()->json(['message' => 'Items deleted successfully'], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Something went wrong'], 500);
            }
        }

        return response()->json(['message' => 'No entries selected'], 400);
    }

    public function deleteTrashAll(Request $request)
    {
        $ids = $request->input('ids');

        if (! empty($ids)) {
            try {

                $trashedUsers = AppUser::onlyTrashed()->whereIn('id', $ids)->get();

                foreach ($trashedUsers as $user) {

                    if ($user->profile_image) {
                        $user->profile_image->delete();
                    }

                    $user->clearMediaCollection('profile_image');

                    $user->forceDelete();
                }

                return response()->json(['message' => 'Items deleted from trash successfully'], Response::HTTP_OK);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return response()->json(['message' => 'No entries selected'], Response::HTTP_BAD_REQUEST);
    }

    public function getHostStatusDetail(Request $request)
    {

        $userId = $request->input('user_id');

        $hostFormData = AppUserMeta::where('user_id', $userId)
            ->where('meta_key', 'host_form_data')
            ->pluck('meta_value')
            ->first();

        $hostFormData = json_decode($hostFormData, true);

        $appUser = AppUser::find($userId);

        $image = $appUser && $appUser->identity_image ? $appUser->identity_image->getUrl() : null;

        // Add the image URL to the response data
        $hostFormData['image'] = $image;

        if (isset($hostFormData['host_status'])) {
            unset($hostFormData['host_status']);
        }

        if (Gate::denies('app_user_contact_access')) {

            if (isset($hostFormData['phone'])) {
                $hostFormData['phone'] = $this->maskPhoneNumber($hostFormData['phone']);
            }

            if (isset($hostFormData['email'])) {
                $hostFormData['email'] = $this->maskEmail($hostFormData['email']);
            }
        }

        return response()->json(['data' => $hostFormData]);
    }

    private function maskPhoneNumber($phone)
    {
        return substr($phone, 0, -4).str_repeat('*', 4);
    }

    private function maskEmail($email)
    {
        [$user, $domain] = explode('@', $email);
        $maskedUser = substr($user, 0, 1).str_repeat('*', max(strlen($user) - 2, 0)).substr($user, -1);

        return $maskedUser.'@'.$domain;
    }

    public function getVerificationDocuments(Request $request)
    {

        try {
            $user = AppUser::find($request->user_id);

            if (! $user) {
                return response()->json(['status' => false, 'message' => 'User not found.'], 404);
            }

            $documentFields = [
                'driving_licence',
                'driver_authorization',
                'hire_service_licence',
                'inspection_certificate',
            ];

            $documents = [];

            foreach ($documentFields as $field) {
                $image = $user->hasMedia($field) ? $user->getFirstMediaUrl($field) : null;
                $status = AppUserMeta::where('user_id', $user->id)
                    ->where('meta_key', $field.'_status')
                    ->value('meta_value') ?? 'pending';

                $documents[$field] = [
                    'image' => $image,
                    'status' => $status,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'User documents retrieved successfully.',
                'data' => [
                    'user_id' => $user->id,
                    'documents' => $documents,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateVerificationDocumentStatus(Request $request)
    {
        try {
            $user = AppUser::where('id', $request->user_id)->first();

            if (! $user) {
                return response()->json(['success' => false, 'message' => trans('global.user_not_found')], 404);
            }

            AppUserMeta::updateOrCreate(
                ['user_id' => $user->id, 'meta_key' => $request->meta_key.'_status'],
                ['meta_value' => $request->status]
            );

            return response()->json([
                'success' => true,
                'message' => trans('global.status_updated_successfully'),
                'status' => $request->status,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function riderAccountView(Request $request, $userId)
    {
        $appUser = AppUser::where('id', $userId)->firstOrFail();
        $packages = AllPackage::pluck('package_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $appUser->load('package');

        return view('admin.appUsers.rider.account', compact(
            'appUser',
            'userId',
            'packages'
        ));
    }

    public function updateProfile(UpdateAppUserRequest $request, $host_id)
    {
        $appUser = AppUser::findOrFail($host_id);
        $userEmail = AppUser::where('email', $request->email)->first();
        if ($userEmail && $userEmail->id !== $appUser->id) {
            return redirect()->to("admin/driver/account/{$appUser->id}")
                ->withErrors(['email' => 'Email already exists.']);
        }
        $userPhone = AppUser::where('phone', $request->phone)->first();
        if ($userPhone && $userPhone->id !== $appUser->id) {
            return redirect()->to("admin/driver/account/{$appUser->id}")
                ->withErrors(['phone' => 'Phone number already exists.']);
        }
        $data = $request->except(['password', 'host_status', 'user_type']);
        if (! empty($request->input('password'))) {
            $data['password'] = Hash::make($request->input('password'));
        }
        $appUser->update($data);
        if ($request->input('profile_image', false)) {
            if (! $appUser->profile_image || $request->input('profile_image') !== $appUser->profile_image->file_name) {
                if ($appUser->profile_image) {
                    $appUser->profile_image->delete();
                }
                $appUser->addMedia(storage_path('tmp/uploads/'.basename($request->input('profile_image'))))
                    ->toMediaCollection('profile_image');
            }
        } elseif ($appUser->profile_image) {
            $appUser->profile_image->delete();
        }

        return redirect()->to("admin/app-users/account/{$appUser->id}")
            ->with('success', 'Profile updated successfully.');
    }

    public function payoutMethodStripe(Request $request, $userId)
    {

        $appUser = AppUser::findOrFail($userId);

        $stripeMeta = $appUser->metadata()->where('meta_key', 'stripe')->first();
        $stripeDetails = $stripeMeta ? json_decode($stripeMeta->meta_value, true) : [];

        $allMethods = PayoutMethod::all();

        // dd($stripeDetails);
        return view('admin.appUsers.driver.payout_method', [
            'appUser' => $appUser,
            'allMethods' => $allMethods,
            'stripeDetails' => $stripeDetails,
        ]);
    }

    public function payoutMethodPaypal(Request $request, $userId)
    {
        $appUser = AppUser::findOrFail($userId);

        $stripeMeta = $appUser->metadata()->where('meta_key', 'paypal')->first();
        $stripeDetails = $stripeMeta ? json_decode($stripeMeta->meta_value, true) : [];

        $allMethods = PayoutMethod::all();

        return view('admin.appUsers.driver.paypal_payout_method', [
            'appUser' => $appUser,
            'allMethods' => $allMethods,
            'stripeDetails' => $stripeDetails,
        ]);
    }

    public function payoutMethodUpi(Request $request, $userId)
    {
        $appUser = AppUser::findOrFail($userId);

        $stripeMeta = $appUser->metadata()->where('meta_key', 'upi')->first();
        $stripeDetails = $stripeMeta ? json_decode($stripeMeta->meta_value, true) : [];

        $allMethods = PayoutMethod::all();

        return view('admin.appUsers.driver.upi_payout_method', [
            'appUser' => $appUser,
            'allMethods' => $allMethods,
            'stripeDetails' => $stripeDetails,
        ]);
    }

    public function payoutMethodBank(Request $request, $userId)
    {
        $appUser = AppUser::findOrFail($userId);

        $stripeMeta = $appUser->metadata()->where('meta_key', 'bank account')->first();
        $stripeDetails = $stripeMeta ? json_decode($stripeMeta->meta_value, true) : [];

        $allMethods = PayoutMethod::all();

        return view('admin.appUsers.driver.bank_payout_method', [
            'appUser' => $appUser,
            'allMethods' => $allMethods,
            'stripeDetails' => $stripeDetails,
        ]);
    }

    public function updatePayoutMethod(Request $request, $userId)
    {
        // dd($request->all());
        $user = AppUser::findOrFail($userId);

        $payoutMethodName = strtolower($request->input('payout_method_name'));

        if ($payoutMethodName === 'bank') {
            $payoutMethodName = 'bank account';
        }

        $payoutMethod = PayoutMethod::where('name', 'LIKE', $payoutMethodName)->first();

        if (! $payoutMethod) {
            return response()->json([
                'message' => 'Invalid payout method!',
            ], 422);
        }

        $rules = $payoutMethodName === 'bank account'
            ? [
                'account_name' => 'required|string',
                'bank_name' => 'required|string',
                'branch_name' => 'nullable|string',
                'account_number' => 'required|string',
                'iban' => 'nullable|string',
                'swift_code' => 'nullable|string',
            ]
            : [
                'email' => 'required|string',
                'note' => 'nullable|string',
            ];

        $validated = $request->validate($rules);

        $validated['id'] = $payoutMethod->id;
        $validated['is_active'] = $request->input('is_active', 0);

        AppUserMeta::updateOrCreate(
            ['user_id' => $user->id, 'meta_key' => $payoutMethodName],
            ['meta_value' => json_encode($validated)]
        );

        if ($payoutMethodName === 'bank account') {
            $payoutMethodName = 'bank';
        }

        return redirect()->route('admin.driver.'.$payoutMethodName, $userId)->with('success', 'Payout method updated successfully!');
    }
}
