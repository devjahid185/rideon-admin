<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\PushNotificationTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\AppUser;
use App\Models\GeneralSetting;
use App\Models\Modern\FoodCategory;
use App\Models\Modern\FoodItem;
use App\Models\Modern\FoodOrder;
use App\Models\Modern\FoodOrderStatusLog;
use App\Models\Modern\Restaurant;
use App\Models\Modern\RestaurantBranch;
use App\Models\Payout;
use App\Models\VendorWallet;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RestaurantOwnerApiController extends Controller
{
    use PushNotificationTrait, ResponseTrait;

    public function register(Request $request)
    {
        Log::info('Restaurant owner register request received', [
            'ip' => $request->ip(),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'has_password' => $request->filled('password'),
        ]);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:191',
            'last_name' => 'nullable|string|max:191',
            'email' => 'required|email|max:191|unique:app_users,email',
            'phone' => 'nullable|string|max:50',
            'phone_country' => 'nullable|string|max:10',
            'password' => 'required|string|min:6|max:191',
        ]);

        if ($validator->fails()) {
            Log::warning('Restaurant owner register validation failed', [
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'errors' => $validator->errors()->toArray(),
            ]);

            return $this->errorComputing($validator);
        }

        try {
            $user = AppUser::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'phone_country' => $request->input('phone_country'),
                'password' => Hash::make($request->input('password')),
                'token' => Str::random(120),
                'status' => true,
                'verified' => 1,
                'user_type' => 'restaurant_owner',
            ]);
        } catch (\Throwable $e) {
            Log::error('Restaurant owner register failed while creating app user', [
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->addErrorResponse(500, 'Unable to create restaurant owner account', null);
        }

        Log::info('Restaurant owner registered successfully', [
            'owner_id' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone,
        ]);

        return $this->addSuccessResponse(200, 'Restaurant owner account created successfully', [
            'token' => $user->token,
            'user' => $this->ownerPayload($user),
        ]);
    }

    public function login(Request $request)
    {
        Log::info('Restaurant owner login request received', [
            'ip' => $request->ip(),
            'login' => $request->input('login'),
        ]);

        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:191',
            'password' => 'required|string|max:191',
        ]);

        if ($validator->fails()) {
            Log::warning('Restaurant owner login validation failed', [
                'login' => $request->input('login'),
                'errors' => $validator->errors()->toArray(),
            ]);

            return $this->errorComputing($validator);
        }

        $login = trim($request->input('login'));
        $user = AppUser::query()
            ->where('user_type', 'restaurant_owner')
            ->where(function ($query) use ($login) {
                $query->where('email', $login)->orWhere('phone', $login);
            })
            ->first();

        if (! $user || ! Hash::check($request->input('password'), (string) $user->password)) {
            Log::warning('Restaurant owner login credential mismatch', [
                'login' => $login,
                'owner_found' => (bool) $user,
            ]);

            return $this->addErrorResponse(401, 'Invalid owner credentials', null);
        }

        if ((string) $user->status === '0') {
            Log::warning('Restaurant owner login blocked because account inactive', [
                'owner_id' => $user->id,
                'login' => $login,
            ]);

            return $this->addErrorResponse(403, 'Restaurant owner account is inactive', null);
        }

        $user->update(['token' => Str::random(120)]);

        return $this->addSuccessResponse(200, 'Restaurant owner logged in successfully', [
            'token' => $user->token,
            'user' => $this->ownerPayload($user),
            'restaurant' => $this->loadOwnerRestaurantRelations($this->ownedRestaurant($user)),
        ]);
    }

    public function me(Request $request)
    {
        $owner = $this->resolveOwner($request);
        if (! $owner) {
            return $this->invalidOwnerToken();
        }

        return $this->addSuccessResponse(200, 'Owner profile fetched successfully', [
            'user' => $this->ownerPayload($owner),
            'restaurant' => $this->loadOwnerRestaurantRelations($this->ownedRestaurant($owner)),
        ]);
    }

    public function dashboard(Request $request)
    {
        $owner = $this->resolveOwner($request);
        if (! $owner) {
            return $this->invalidOwnerToken();
        }

        $restaurant = $this->ownedRestaurant($owner);
        if (! $restaurant) {
            return $this->addSuccessResponse(200, 'Restaurant not onboarded yet', [
                'needs_onboarding' => true,
                'summary' => $this->emptySummary(),
                'restaurant' => null,
            ]);
        }

        $today = now()->toDateString();
        $ordersQuery = FoodOrder::where('restaurant_id', $restaurant->id)
            ->where('status', '!=', FoodOrder::STATUS_PAYMENT_PENDING);
        $walletSummary = $this->ownerWalletSummary($owner->id);
        $summary = [
            'today_orders' => (clone $ordersQuery)->whereDate('created_at', $today)->count(),
            'active_orders' => (clone $ordersQuery)->whereIn('status', [
                FoodOrder::STATUS_PLACED,
                FoodOrder::STATUS_ACCEPTED,
                FoodOrder::STATUS_PREPARING,
                FoodOrder::STATUS_READY_FOR_PICKUP,
                FoodOrder::STATUS_PICKED_UP,
                FoodOrder::STATUS_ON_THE_WAY,
            ])->count(),
            'today_revenue' => $this->ownerWalletCreditTotal($owner->id, $today),
            'total_revenue' => $walletSummary['total_earning'],
            'wallet' => $walletSummary,
            'menu_items' => FoodItem::where('restaurant_id', $restaurant->id)->count(),
            'branches' => RestaurantBranch::where('restaurant_id', $restaurant->id)->where('is_active', true)->count(),
        ];

        $orders = FoodOrder::query()
            ->with([
                'customer:id,first_name,last_name,email,phone,phone_country',
                'driver:id,first_name,last_name,phone,phone_country',
                'branch:id,name,address,latitude,longitude',
            ])
            ->where('restaurant_id', $restaurant->id)
            ->where('status', '!=', FoodOrder::STATUS_PAYMENT_PENDING)
            ->latest('id')
            ->limit((int) $request->input('limit', 20))
            ->get();
        $this->hydrateCustomerSnapshots($orders);

        return $this->addSuccessResponse(200, 'Owner dashboard fetched successfully', [
            'needs_onboarding' => false,
            'summary' => $summary,
            'restaurant' => $this->loadOwnerRestaurantRelations($restaurant, ['items.category']),
            'orders' => $orders,
        ]);
    }

    public function onboardRestaurant(Request $request)
    {
        Log::info('Restaurant onboarding request received', [
            'ip' => $request->ip(),
            'restaurant_name' => $request->input('restaurant_name'),
            'branch_name' => $request->input('branch_name'),
            'has_logo_file' => $request->hasFile('logo_image_file'),
            'has_cover_file' => $request->hasFile('cover_image_file'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);

        $owner = $this->resolveOwner($request);
        if (! $owner) {
            Log::warning('Restaurant onboarding failed because owner token invalid', [
                'has_request_token' => $request->filled('token'),
                'has_header_token' => $request->headers->has('x-auth-token'),
            ]);

            return $this->invalidOwnerToken();
        }

        $validator = Validator::make($request->all(), [
            'restaurant_name' => 'required|string|max:191',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'description' => 'nullable|string|max:5000',
            'logo_image' => 'nullable|string|max:500',
            'cover_image' => 'nullable|string|max:500',
            'logo_image_file' => 'nullable|image|max:4096',
            'cover_image_file' => 'nullable|image|max:4096',
            'branch_name' => 'required|string|max:191',
            'branch_phone' => 'nullable|string|max:50',
            'address' => 'required|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'delivery_radius_km' => 'nullable|numeric|min:0|max:50',
            'min_delivery_time_minutes' => 'nullable|integer|min:0|max:300',
            'max_delivery_time_minutes' => 'nullable|integer|min:0|max:300',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            Log::warning('Restaurant onboarding validation failed', [
                'owner_id' => $owner->id,
                'restaurant_name' => $request->input('restaurant_name'),
                'errors' => $validator->errors()->toArray(),
            ]);

            return $this->errorComputing($validator);
        }

        try {
            $restaurant = DB::transaction(function () use ($request, $owner) {
                $restaurant = $this->ownedRestaurant($owner);
                $restaurantData = [
                    'owner_id' => $owner->id,
                    'name' => $request->input('restaurant_name'),
                    'phone' => $request->input('phone'),
                    'email' => $request->input('email'),
                    'description' => $request->input('description'),
                    'is_active' => true,
                ];

                if ($request->filled('logo_image')) {
                    $restaurantData['logo_image'] = $request->input('logo_image');
                }
                if ($request->filled('cover_image')) {
                    $restaurantData['cover_image'] = $request->input('cover_image');
                }
                if ($request->hasFile('logo_image_file')) {
                    Log::info('Restaurant onboarding storing logo image', ['owner_id' => $owner->id]);
                    $restaurantData['logo_image'] = $this->storeImage($request->file('logo_image_file'));
                }
                if ($request->hasFile('cover_image_file')) {
                    Log::info('Restaurant onboarding storing cover image', ['owner_id' => $owner->id]);
                    $restaurantData['cover_image'] = $this->storeImage($request->file('cover_image_file'));
                }

                if ($restaurant) {
                    Log::info('Restaurant onboarding updating existing restaurant', [
                        'owner_id' => $owner->id,
                        'restaurant_id' => $restaurant->id,
                    ]);
                    $restaurant->update($restaurantData);
                } else {
                    $restaurantData['slug'] = $this->uniqueRestaurantSlug($request->input('restaurant_name'));
                    $restaurant = Restaurant::create($restaurantData);
                    Log::info('Restaurant onboarding created restaurant', [
                        'owner_id' => $owner->id,
                        'restaurant_id' => $restaurant->id,
                    ]);
                }

                $branch = RestaurantBranch::updateOrCreate(
                    ['restaurant_id' => $restaurant->id, 'name' => $request->input('branch_name')],
                    [
                        'contact_phone' => $request->input('branch_phone', $request->input('phone')),
                        'address' => $request->input('address'),
                        'latitude' => $request->input('latitude'),
                        'longitude' => $request->input('longitude'),
                        'delivery_radius_km' => $request->input('delivery_radius_km', 8),
                        'min_delivery_time_minutes' => $request->input('min_delivery_time_minutes', 20),
                        'max_delivery_time_minutes' => $request->input('max_delivery_time_minutes', 45),
                        'opening_time' => $request->input('opening_time'),
                        'closing_time' => $request->input('closing_time'),
                        'is_open' => true,
                        'is_active' => true,
                    ]
                );

                Log::info('Restaurant onboarding saved branch', [
                    'owner_id' => $owner->id,
                    'restaurant_id' => $restaurant->id,
                    'branch_id' => $branch->id,
                ]);

                return $restaurant;
            });
        } catch (\Throwable $e) {
            Log::error('Restaurant onboarding failed', [
                'owner_id' => $owner->id,
                'restaurant_name' => $request->input('restaurant_name'),
                'branch_name' => $request->input('branch_name'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->addErrorResponse(500, 'Unable to onboard restaurant', null);
        }

        Log::info('Restaurant onboarding completed successfully', [
            'owner_id' => $owner->id,
            'restaurant_id' => $restaurant->id,
        ]);

        return $this->addSuccessResponse(200, 'Restaurant onboarded successfully', $this->loadOwnerRestaurantRelations($restaurant));
    }

    public function saveCategory(Request $request, ?int $id = null)
    {
        $owner = $this->resolveOwner($request);
        if (! $owner) {
            return $this->invalidOwnerToken();
        }
        $restaurant = $this->ownedRestaurantOrError($owner);
        if (! $restaurant instanceof Restaurant) {
            return $restaurant;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $category = $id
            ? FoodCategory::where('restaurant_id', $restaurant->id)->findOrFail($id)
            : new FoodCategory(['restaurant_id' => $restaurant->id]);

        $category->fill([
            'name' => $request->input('name'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => (bool) $request->input('is_active', true),
        ])->save();

        return $this->addSuccessResponse(200, 'Category saved successfully', $category);
    }

    public function saveItem(Request $request, ?int $id = null)
    {
        $owner = $this->resolveOwner($request);
        if (! $owner) {
            return $this->invalidOwnerToken();
        }
        $restaurant = $this->ownedRestaurantOrError($owner);
        if (! $restaurant instanceof Restaurant) {
            return $restaurant;
        }

        $validator = Validator::make($request->all(), [
            'food_category_id' => 'required|exists:food_categories,id',
            'name' => 'required|string|max:191',
            'description' => 'nullable|string|max:5000',
            'base_price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|max:4096',
            'is_veg' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        if (! FoodCategory::where('restaurant_id', $restaurant->id)->where('id', $request->input('food_category_id'))->exists()) {
            return $this->addErrorResponse(422, 'Invalid category for this restaurant', null);
        }

        $item = $id
            ? FoodItem::where('restaurant_id', $restaurant->id)->findOrFail($id)
            : new FoodItem(['restaurant_id' => $restaurant->id]);

        $data = [
            'food_category_id' => $request->input('food_category_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'base_price' => $request->input('base_price'),
            'is_veg' => (bool) $request->input('is_veg', false),
            'is_available' => (bool) $request->input('is_available', true),
        ];
        if ($request->filled('image')) {
            $data['image'] = $request->input('image');
        }
        if ($request->hasFile('image_file')) {
            $data['image'] = $this->storeImage($request->file('image_file'));
        }

        $item->fill($data)->save();

        return $this->addSuccessResponse(200, 'Food item saved successfully', $item->load('category'));
    }

    public function saveBranch(Request $request, ?int $branchId = null)
    {
        $owner = $this->resolveOwner($request);
        if (! $owner) {
            return $this->invalidOwnerToken();
        }
        $restaurant = $this->ownedRestaurantOrError($owner);
        if (! $restaurant instanceof Restaurant) {
            return $restaurant;
        }

        Log::info('Restaurant owner branch save requested', [
            'owner_id' => $owner->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branchId,
            'name' => $request->input('name'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'required|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'delivery_radius_km' => 'nullable|numeric|min:0|max:50',
            'min_delivery_time_minutes' => 'nullable|integer|min:0|max:300',
            'max_delivery_time_minutes' => 'nullable|integer|min:0|max:300',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'is_open' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            Log::warning('Restaurant owner branch validation failed', [
                'owner_id' => $owner->id,
                'restaurant_id' => $restaurant->id,
                'branch_id' => $branchId,
                'errors' => $validator->errors()->toArray(),
            ]);

            return $this->errorComputing($validator);
        }

        $branch = $branchId
            ? RestaurantBranch::where('restaurant_id', $restaurant->id)->findOrFail($branchId)
            : new RestaurantBranch(['restaurant_id' => $restaurant->id]);

        $branch->fill([
            'name' => $request->input('name'),
            'contact_phone' => $request->input('contact_phone'),
            'address' => $request->input('address'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'delivery_radius_km' => $request->input('delivery_radius_km', $branch->delivery_radius_km ?? 8),
            'min_delivery_time_minutes' => $request->input('min_delivery_time_minutes', $branch->min_delivery_time_minutes ?? 20),
            'max_delivery_time_minutes' => $request->input('max_delivery_time_minutes', $branch->max_delivery_time_minutes ?? 45),
            'opening_time' => $request->input('opening_time', $branch->opening_time),
            'closing_time' => $request->input('closing_time', $branch->closing_time),
            'is_open' => (bool) $request->input('is_open', $branch->exists ? $branch->is_open : true),
            'is_active' => true,
        ])->save();

        Log::info('Restaurant owner branch saved', [
            'owner_id' => $owner->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
        ]);

        return $this->addSuccessResponse(200, 'Branch saved successfully', $branch);
    }

    public function updateBranchAvailability(Request $request, int $branchId)
    {
        $owner = $this->resolveOwner($request);
        if (! $owner) {
            return $this->invalidOwnerToken();
        }
        $restaurant = $this->ownedRestaurantOrError($owner);
        if (! $restaurant instanceof Restaurant) {
            return $restaurant;
        }

        $validator = Validator::make($request->all(), [
            'is_open' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $branch = RestaurantBranch::where('restaurant_id', $restaurant->id)->findOrFail($branchId);
        $branch->update(['is_open' => (bool) $request->input('is_open')]);

        return $this->addSuccessResponse(200, 'Branch availability updated successfully', $branch);
    }

    public function deleteBranch(Request $request, int $branchId)
    {
        $owner = $this->resolveOwner($request);
        if (! $owner) {
            return $this->invalidOwnerToken();
        }
        $restaurant = $this->ownedRestaurantOrError($owner);
        if (! $restaurant instanceof Restaurant) {
            return $restaurant;
        }

        $activeBranchCount = RestaurantBranch::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->count();
        if ($activeBranchCount <= 1) {
            return $this->addErrorResponse(422, 'At least one active branch is required', null);
        }

        $branch = RestaurantBranch::where('restaurant_id', $restaurant->id)->findOrFail($branchId);
        $branch->update([
            'is_open' => false,
            'is_active' => false,
        ]);

        Log::info('Restaurant owner branch deleted', [
            'owner_id' => $owner->id,
            'restaurant_id' => $restaurant->id,
            'branch_id' => $branch->id,
        ]);

        return $this->addSuccessResponse(200, 'Branch deleted successfully', ['branch_id' => $branch->id]);
    }

    public function orders(Request $request)
    {
        $owner = $this->resolveOwner($request);
        if (! $owner) {
            return $this->invalidOwnerToken();
        }
        $restaurant = $this->ownedRestaurantOrError($owner);
        if (! $restaurant instanceof Restaurant) {
            return $restaurant;
        }

        $orders = FoodOrder::query()
            ->with(['items.foodItem', 'customer:id,first_name,last_name,email,phone,phone_country', 'driver:id,first_name,last_name,phone,phone_country', 'branch:id,name,address,latitude,longitude'])
            ->where('restaurant_id', $restaurant->id)
            ->where('status', '!=', FoodOrder::STATUS_PAYMENT_PENDING)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest('id')
            ->limit((int) $request->input('limit', 50))
            ->get();
        $this->hydrateCustomerSnapshots($orders);

        return $this->addSuccessResponse(200, 'Restaurant orders fetched successfully', $orders);
    }

    public function updateOrderStatus(Request $request, int $orderId)
    {
        $owner = $this->resolveOwner($request);
        if (! $owner) {
            return $this->invalidOwnerToken();
        }
        $restaurant = $this->ownedRestaurantOrError($owner);
        if (! $restaurant instanceof Restaurant) {
            return $restaurant;
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:accepted,preparing,ready_for_pickup,cancelled',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $order = FoodOrder::where('restaurant_id', $restaurant->id)->findOrFail($orderId);
        $oldStatus = $order->status;
        $newStatus = $request->input('status');
        $allowed = [
            FoodOrder::STATUS_PLACED => [FoodOrder::STATUS_ACCEPTED, FoodOrder::STATUS_CANCELLED],
            FoodOrder::STATUS_ACCEPTED => [FoodOrder::STATUS_PREPARING, FoodOrder::STATUS_CANCELLED],
            FoodOrder::STATUS_PREPARING => [FoodOrder::STATUS_READY_FOR_PICKUP, FoodOrder::STATUS_CANCELLED],
        ];

        if (! in_array($newStatus, $allowed[$oldStatus] ?? [], true)) {
            return $this->addErrorResponse(422, "Invalid restaurant status transition from {$oldStatus} to {$newStatus}", null);
        }

        $updates = ['status' => $newStatus];
        if ($newStatus === FoodOrder::STATUS_ACCEPTED) {
            $updates['accepted_at'] = now();
        }
        $order->update($updates);
        FoodOrderStatusLog::create([
            'food_order_id' => $order->id,
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'changed_by_user_id' => $owner->id,
            'note' => $request->input('note', 'Updated by restaurant owner'),
        ]);

        $freshOrder = $order->fresh(['items.foodItem', 'customer', 'driver', 'branch', 'restaurant']);
        $this->notifyRestaurantOrderStatusUpdated($freshOrder, $oldStatus, $newStatus);

        return $this->addSuccessResponse(200, 'Order status updated successfully', $freshOrder);
    }

    public function wallet(Request $request)
    {
        $owner = $this->resolveOwner($request);
        if (! $owner) {
            return $this->invalidOwnerToken();
        }

        $limit = min(max((int) $request->input('limit', 20), 1), 100);
        $offset = max((int) $request->input('offset', 0), 0);
        $summary = $this->ownerWalletSummary($owner->id);

        $transactions = VendorWallet::where('vendor_id', $owner->id)
            ->orderByDesc('id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(function (VendorWallet $wallet) {
                return [
                    'id' => $wallet->id,
                    'token' => $wallet->token,
                    'food_order_id' => (int) $wallet->booking_id,
                    'payout_id' => (int) $wallet->payout_id,
                    'amount' => (float) $wallet->amount,
                    'type' => $wallet->type,
                    'description' => $wallet->description,
                    'created_at' => optional($wallet->created_at)->format('Y-m-d H:i:s'),
                ];
            });

        $withdrawals = Payout::where('vendorid', $owner->id)
            ->where('module', 3)
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function (Payout $payout) {
                return [
                    'id' => $payout->id,
                    'amount' => (float) $payout->amount,
                    'currency' => $payout->currency,
                    'payment_method' => $payout->payment_method,
                    'payout_status' => $payout->payout_status,
                    'note' => $payout->note,
                    'created_at' => optional($payout->created_at)->format('Y-m-d H:i:s'),
                    'updated_at' => optional($payout->updated_at)->format('Y-m-d H:i:s'),
                ];
            });

        $nextOffset = $transactions->isEmpty() ? -1 : $offset + $transactions->count();

        return $this->addSuccessResponse(200, 'Restaurant owner wallet fetched successfully', [
            'summary' => $summary,
            'transactions' => $transactions,
            'withdrawals' => $withdrawals,
            'offset' => $nextOffset,
            'limit' => $limit,
        ]);
    }

    public function requestWithdrawal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string|max:10',
            'payment_method' => 'nullable|string|max:191',
            'note' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $owner = $this->resolveOwner($request);
        if (! $owner) {
            return $this->invalidOwnerToken();
        }

        $amount = round((float) $request->input('amount'), 2);
        $summary = $this->ownerWalletSummary($owner->id);
        $available = (float) $summary['available_balance'];

        if ($amount > $available) {
            return $this->addErrorResponse(422, 'Withdrawal amount exceeds available wallet balance', [
                'available_balance' => $available,
            ]);
        }

        $currency = $request->input('currency')
            ?: (GeneralSetting::where('meta_key', 'general_default_currency')->value('meta_value') ?: 'USD');

        $payout = DB::transaction(function () use ($owner, $amount, $currency, $request) {
            return Payout::create([
                'vendorid' => $owner->id,
                'amount' => $amount,
                'currency' => $currency,
                'request_by' => 'restaurant_owner',
                'payment_method' => $request->input('payment_method', 'manual'),
                'payout_status' => 'Pending',
                'note' => $request->input('note'),
                'module' => 3,
            ]);
        });

        return $this->addSuccessResponse(200, 'Withdrawal request submitted successfully', [
            'payout' => $payout,
            'summary' => $this->ownerWalletSummary($owner->id),
        ]);
    }

    private function notifyRestaurantOrderStatusUpdated(FoodOrder $order, string $oldStatus, string $newStatus): void
    {
        try {
            $data = [
                'route' => 'food_order',
                'food_order_id' => (string) $order->id,
                'food_order_status' => (string) $newStatus,
                'food_order_prev_status' => (string) $oldStatus,
                'food_order_number' => (string) $order->order_number,
            ];

            if ($order->customer && ! empty($order->customer->fcm)) {
                $this->sendFcmMessage(
                    $order->customer->fcm,
                    'Food Order Update',
                    "Order {$order->order_number} status: {$newStatus}",
                    $data,
                    0,
                    'user'
                );
            }

            if ($order->driver && ! empty($order->driver->fcm)) {
                $driverMessage = $newStatus === FoodOrder::STATUS_READY_FOR_PICKUP
                    ? "Order {$order->order_number} is ready for pickup."
                    : "Restaurant updated {$order->order_number} to {$newStatus}.";

                $this->sendFcmMessage(
                    $order->driver->fcm,
                    $newStatus === FoodOrder::STATUS_READY_FOR_PICKUP
                        ? 'Food Order Ready for Pickup'
                        : 'Restaurant Order Update',
                    $driverMessage,
                    $data,
                    0,
                    'driver'
                );
            }
        } catch (\Throwable $e) {
            Log::error('Restaurant owner food status notification failed', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function resolveOwner(Request $request): ?AppUser
    {
        $token = (string) $request->input('token', '');
        if ($token === '') {
            $token = (string) $request->header('x-auth-token', '');
        }
        if ($token === '') {
            return null;
        }

        return AppUser::where('token', $token)
            ->where('user_type', 'restaurant_owner')
            ->first();
    }

    private function hydrateCustomerSnapshots($orders): void
    {
        foreach ($orders as $order) {
            if (! $order->customer) {
                continue;
            }

            if (empty($order->customer_name)) {
                $profileName = trim(($order->customer->first_name ?? '').' '.($order->customer->last_name ?? ''));
                $order->customer_name = $profileName !== ''
                    ? $profileName
                    : ($order->customer->email ?: ($order->customer->phone ?: 'Customer'));
            }
            if (empty($order->customer_email)) {
                $order->customer_email = $order->customer->email;
            }
            if (empty($order->customer_phone)) {
                $order->customer_phone = $order->customer->phone;
            }
            if (empty($order->customer_phone_country)) {
                $order->customer_phone_country = $order->customer->phone_country;
            }
        }
    }

    private function ownedRestaurant(AppUser $owner): ?Restaurant
    {
        return Restaurant::where('owner_id', $owner->id)->latest('id')->first();
    }

    private function ownedRestaurantOrError(AppUser $owner)
    {
        $restaurant = $this->ownedRestaurant($owner);

        return $restaurant ?: $this->addErrorResponse(404, 'Restaurant not onboarded yet', null);
    }

    private function loadOwnerRestaurantRelations(?Restaurant $restaurant, array $extra = []): ?Restaurant
    {
        if (! $restaurant) {
            return null;
        }

        $restaurant->load(array_merge([
            'branches' => function ($query) {
                $query->where('is_active', true)->orderByDesc('is_open')->orderBy('id');
            },
            'categories',
            'items',
        ], $extra));

        return $restaurant;
    }

    private function ownerPayload(AppUser $user): array
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'phone_country' => $user->phone_country,
            'user_type' => $user->user_type,
            'status' => $user->status,
        ];
    }

    private function emptySummary(): array
    {
        return [
            'today_orders' => 0,
            'active_orders' => 0,
            'today_revenue' => 0,
            'total_revenue' => 0,
            'wallet' => [
                'wallet_balance' => 0,
                'available_balance' => 0,
                'pending_withdrawal' => 0,
                'total_withdrawn' => 0,
                'total_earning' => 0,
                'refunded' => 0,
            ],
            'menu_items' => 0,
            'branches' => 0,
        ];
    }

    private function invalidOwnerToken()
    {
        return $this->addErrorResponse(419, 'Invalid restaurant owner token', null);
    }

    private function ownerWalletSummary(int $ownerId): array
    {
        $walletSums = VendorWallet::selectRaw("
            SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as total_credit,
            SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as total_debit,
            SUM(CASE WHEN type = 'refund' THEN amount ELSE 0 END) as total_refund
        ")
            ->where('vendor_id', $ownerId)
            ->first();

        $payoutSums = Payout::selectRaw("
            SUM(CASE WHEN payout_status = 'Pending' THEN amount ELSE 0 END) as pending_amount,
            SUM(CASE WHEN payout_status = 'Success' THEN amount ELSE 0 END) as withdrawn_amount,
            SUM(CASE WHEN payout_status = 'Rejected' THEN amount ELSE 0 END) as rejected_amount
        ")
            ->where('vendorid', $ownerId)
            ->where('module', 3)
            ->first();

        $totalCredit = (float) ($walletSums->total_credit ?? 0);
        $totalDebit = (float) ($walletSums->total_debit ?? 0);
        $totalRefund = (float) ($walletSums->total_refund ?? 0);
        $pending = (float) ($payoutSums->pending_amount ?? 0);
        $withdrawn = (float) ($payoutSums->withdrawn_amount ?? 0);
        $walletBalance = $totalCredit - $totalDebit - $totalRefund;
        $availableBalance = max(0, $walletBalance - $pending);

        return [
            'wallet_balance' => round($walletBalance, 2),
            'available_balance' => round($availableBalance, 2),
            'pending_withdrawal' => round($pending, 2),
            'total_withdrawn' => round($withdrawn, 2),
            'total_earning' => round($totalCredit, 2),
            'refunded' => round($totalRefund, 2),
        ];
    }

    private function ownerRevenueTotal($query): float
    {
        return round((float) $query
            ->selectRaw('COALESCE(SUM(GREATEST((items_subtotal - tax_amount - discount_amount), 0)), 0) as owner_revenue')
            ->value('owner_revenue'), 2);
    }

    private function ownerWalletCreditTotal(int $ownerId, ?string $date = null): float
    {
        $query = VendorWallet::where('vendor_id', $ownerId)
            ->where('type', 'credit');

        if ($date !== null) {
            $query->whereDate('created_at', $date);
        }

        return round((float) $query->sum('amount'), 2);
    }

    private function uniqueRestaurantSlug(string $name): string
    {
        do {
            $slug = Str::slug($name).'-'.Str::lower(Str::random(5));
        } while (Restaurant::where('slug', $slug)->exists());

        return $slug;
    }

    private function storeImage(UploadedFile $file): string
    {
        Log::info('Restaurant owner image upload started', [
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        $path = $file->store('food', 'public');

        Log::info('Restaurant owner image upload completed', [
            'path' => $path,
            'url' => asset('storage/'.$path),
        ]);

        return asset('storage/'.$path);
    }
}
