<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\PushNotificationTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\AppUser;
use App\Models\Modern\FoodItem;
use App\Models\Modern\FoodOrder;
use App\Models\Modern\FoodOrderItem;
use App\Models\Modern\FoodOrderLocationTrack;
use App\Models\Modern\FoodOrderStatusLog;
use App\Models\Modern\Restaurant;
use App\Models\Modern\RestaurantBranch;
use App\Models\VendorWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FoodDeliveryApiController extends Controller
{
    use PushNotificationTrait, ResponseTrait;

    public function nearbyRestaurants(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'nullable|numeric',
            'longtitude' => 'nullable|numeric',
            'radius_km' => 'nullable|numeric|min:1|max:30',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $lat = (float) $request->input('latitude');
        $lngRaw = $request->input('longitude', $request->input('longtitude'));
        if ($lngRaw === null || $lngRaw === '') {
            return $this->addErrorResponse(422, 'The longitude field is required.', null);
        }
        $lng = (float) $lngRaw;
        $radiusKm = (float) $request->input('radius_km', 8);

        Log::info('Food nearby restaurants request', [
            'latitude' => $lat,
            'longitude' => $lng,
            'radius_km' => $radiusKm,
        ]);

        try {
            $branches = RestaurantBranch::query()
                ->with([
                    'restaurant' => function ($query) {
                        $query
                            ->select('id', 'name', 'slug', 'rating', 'logo_image', 'cover_image', 'is_active', 'owner_id')
                            ->with([
                                'categories' => function ($categoryQuery) {
                                    $categoryQuery
                                        ->select('id', 'restaurant_id', 'name', 'sort_order', 'is_active')
                                        ->where('is_active', true)
                                        ->orderBy('sort_order');
                                },
                                'categories.items' => function ($itemQuery) {
                                    $itemQuery
                                        ->select('id', 'restaurant_id', 'food_category_id', 'name', 'description', 'base_price', 'image', 'is_available')
                                        ->where('is_available', true);
                                },
                            ]);
                    },
                ])
                ->where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get()
                ->each(function ($branch) use ($lat, $lng) {
                    $branch->distance_km = $this->distanceKm(
                        $lat,
                        $lng,
                        (float) $branch->latitude,
                        (float) $branch->longitude
                    );
                    $this->appendBranchAvailability($branch);
                })
                ->filter(function ($branch) {
                    return $branch->restaurant && $branch->restaurant->is_active;
                })
                ->filter(function ($branch) use ($radiusKm) {
                    $branchRadius = (float) ($branch->delivery_radius_km ?? 0);
                    return (float) $branch->distance_km <= max($radiusKm, $branchRadius);
                })
                ->sortBy('distance_km')
                ->values();
        } catch (\Throwable $e) {
            Log::error('Food nearby restaurants query failed', [
                'latitude' => $lat,
                'longitude' => $lng,
                'radius_km' => $radiusKm,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->addErrorResponse(500, 'Unable to load nearby restaurants', null);
        }

        Log::info('Food nearby restaurants response', [
            'latitude' => $lat,
            'longitude' => $lng,
            'radius_km' => $radiusKm,
            'count' => $branches->count(),
            'restaurant_ids' => $branches->pluck('restaurant_id')->values()->all(),
            'distances' => $branches->map(fn ($branch) => [
                'branch_id' => $branch->id,
                'restaurant_id' => $branch->restaurant_id,
                'distance_km' => $branch->distance_km,
                'branch_radius_km' => $branch->delivery_radius_km,
            ])->values()->all(),
        ]);

        return $this->addSuccessResponse(200, 'Nearby restaurants fetched successfully', $branches);
    }

    public function restaurantMenu(Request $request, int $restaurantId)
    {
        $restaurant = Restaurant::query()
            ->where('is_active', true)
            ->with([
                'categories' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                },
                'categories.items' => function ($query) {
                    $query->where('is_available', true);
                },
                'categories.items.variants' => function ($query) {
                    $query->where('is_active', true);
                },
                'categories.items.addons' => function ($query) {
                    $query->where('is_active', true);
                },
                'branches' => function ($query) {
                    $query->where('is_active', true);
                },
            ])
            ->find($restaurantId);

        if (! $restaurant) {
            return $this->addErrorResponse(404, 'Restaurant not found', null);
        }

        $restaurant->branches->each(fn ($branch) => $this->appendBranchAvailability($branch));

        return $this->addSuccessResponse(200, 'Restaurant menu fetched successfully', $restaurant);
    }

    public function createFoodOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required|exists:restaurants,id',
            'restaurant_branch_id' => 'required|exists:restaurant_branches,id',
            'delivery_address' => 'required|string|max:1000',
            'delivery_latitude' => 'nullable|numeric',
            'delivery_longitude' => 'nullable|numeric',
            'customer_name' => 'nullable|string|max:191',
            'customer_phone' => 'nullable|string|max:50',
            'customer_phone_country' => 'nullable|string|max:10',
            'customer_email' => 'nullable|email|max:191',
            'payment_method' => 'required|string|in:stripe',
            'payment_status' => 'nullable|string|in:pending',
            'customer_note' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.food_item_variant_id' => 'nullable|exists:food_item_variants,id',
            'items.*.quantity' => 'required|integer|min:1|max:20',
            'items.*.addons' => 'nullable|array',
            'items.*.special_instruction' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = $this->resolveApiUser($request);
        if (! $user) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), null);
        }

        $branch = RestaurantBranch::query()
            ->where('id', $request->input('restaurant_branch_id'))
            ->where('restaurant_id', $request->input('restaurant_id'))
            ->where('is_active', true)
            ->first();

        if (! $branch) {
            return $this->addErrorResponse(422, 'Invalid or inactive restaurant branch', null);
        }

        if (! $this->branchAcceptsOrders($branch)) {
            return $this->addErrorResponse(409, 'This restaurant branch is currently closed. You can browse the menu, but ordering is unavailable right now.', null);
        }

        $order = DB::transaction(function () use ($request, $user) {
            $itemsSubtotal = 0.0;
            $preparedItems = [];

            foreach ($request->input('items', []) as $itemInput) {
                /** @var FoodItem $foodItem */
                $foodItem = FoodItem::query()
                    ->where('id', $itemInput['food_item_id'])
                    ->where('restaurant_id', $request->input('restaurant_id'))
                    ->where('is_available', true)
                    ->firstOrFail();

                $variant = null;
                if (! empty($itemInput['food_item_variant_id'])) {
                    $variant = $foodItem->variants()
                        ->where('id', $itemInput['food_item_variant_id'])
                        ->where('is_active', true)
                        ->first();
                }

                $unitPrice = (float) $foodItem->base_price + (float) ($variant->price_delta ?? 0);
                $lineTotal = $unitPrice * (int) $itemInput['quantity'];
                $itemsSubtotal += $lineTotal;

                $preparedItems[] = [
                    'food_item_id' => (int) $foodItem->id,
                    'food_item_variant_id' => $variant?->id,
                    'quantity' => (int) $itemInput['quantity'],
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'addons' => $itemInput['addons'] ?? [],
                    'special_instruction' => $itemInput['special_instruction'] ?? null,
                ];
            }

            $taxAmount = round($itemsSubtotal * 0.05, 2);
            $deliveryFee = 30.0;
            $platformFee = 5.0;
            $discountAmount = 0.0;
            $totalAmount = round($itemsSubtotal + $taxAmount + $deliveryFee + $platformFee - $discountAmount, 2);

            /** @var FoodOrder $order */
            $order = FoodOrder::create([
                'order_number' => 'FOOD-'.strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'restaurant_id' => (int) $request->input('restaurant_id'),
                'restaurant_branch_id' => (int) $request->input('restaurant_branch_id'),
                'customer_name' => $this->customerSnapshotName($request, $user),
                'customer_email' => $request->input('customer_email', $user->email),
                'customer_phone' => $request->input('customer_phone', $user->phone),
                'customer_phone_country' => $request->input('customer_phone_country', $user->phone_country),
                'delivery_otp' => $this->createFoodDeliveryOtp(),
                'status' => FoodOrder::STATUS_PAYMENT_PENDING,
                'payment_status' => 'pending',
                'payment_method' => $request->input('payment_method'),
                'payment_reference' => null,
                'delivery_address' => $request->input('delivery_address'),
                'delivery_latitude' => $request->input('delivery_latitude'),
                'delivery_longitude' => $request->input('delivery_longitude'),
                'items_subtotal' => $itemsSubtotal,
                'tax_amount' => $taxAmount,
                'delivery_fee' => $deliveryFee,
                'driver_commission' => $deliveryFee,
                'platform_fee' => $platformFee,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'customer_note' => $request->input('customer_note'),
                'placed_at' => null,
            ]);

            foreach ($preparedItems as $preparedItem) {
                $preparedItem['food_order_id'] = $order->id;
                FoodOrderItem::create($preparedItem);
            }

            FoodOrderStatusLog::create([
                'food_order_id' => $order->id,
                'from_status' => null,
                'to_status' => FoodOrder::STATUS_PAYMENT_PENDING,
                'changed_by_user_id' => $user->id,
                'note' => 'Food checkout initiated; awaiting Stripe payment',
            ]);

            return $order->load(['items.foodItem', 'items.variant', 'restaurant', 'branch']);
        });

        $order->setAttribute('payment_url', route('food.payment_methods', [
            'order' => $order->id,
            'token' => $user->token,
        ]));

        return $this->addSuccessResponse(200, 'Food checkout created successfully', $order);
    }

    public function orderDetails(Request $request, int $orderId)
    {
        $validator = Validator::make($request->all(), [
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = $this->resolveApiUser($request);
        if (! $user) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), null);
        }

        $order = FoodOrder::query()
            ->with(['items.foodItem', 'items.variant', 'restaurant', 'branch', 'driver:id,first_name,last_name,phone,phone_country'])
            ->where('id', $orderId)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('driver_id', $user->id);
            })
            ->first();

        if (! $order) {
            return $this->addErrorResponse(404, 'Food order not found', null);
        }

        $this->hideDeliveryOtpUnlessCustomer($order, $user);

        return $this->addSuccessResponse(200, 'Food order details fetched successfully', $order);
    }

    public function listUserOrders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:200',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = $this->resolveApiUser($request);
        if (! $user) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), null);
        }

        $query = FoodOrder::query()
            ->with(['restaurant:id,name,logo_image', 'branch:id,restaurant_id,name,address', 'driver:id,first_name,last_name,phone,phone_country'])
            ->latest('id');

        $requestedUserType = strtolower((string) $request->input('user_type', ''));
        $isDriverContext = $requestedUserType === 'driver' ||
            ($requestedUserType === '' && strtolower((string) $user->user_type) === 'driver');

        if ($isDriverContext) {
            // Drivers may only see food orders assigned to them in their history.
            // Unassigned work is exposed separately through the available-orders API.
            $query->where('driver_id', $user->id);
        } else {
            $email = trim((string) ($user->email ?? ''));
            $phone = trim((string) ($user->phone ?? ''));
            $phoneCountry = trim((string) ($user->phone_country ?? ''));

            $query->where(function ($customerQuery) use ($user, $email, $phone, $phoneCountry) {
                $customerQuery->where('user_id', $user->id);

                if ($email !== '') {
                    $customerQuery->orWhere('customer_email', $email);
                }

                if ($phone !== '') {
                    $customerQuery->orWhere(function ($phoneQuery) use ($phone, $phoneCountry) {
                        $phoneQuery->where('customer_phone', $phone);

                        if ($phoneCountry !== '') {
                            $phoneQuery->where(function ($countryQuery) use ($phoneCountry) {
                                $countryQuery
                                    ->where('customer_phone_country', $phoneCountry)
                                    ->orWhereNull('customer_phone_country')
                                    ->orWhere('customer_phone_country', '');
                            });
                        }
                    });
                }
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->get();
        $orders->each(fn (FoodOrder $order) => $this->hideDeliveryOtpUnlessCustomer($order, $user));

        Log::info('Food user orders response', [
            'user_id' => $user->id,
            'status_filter' => $request->input('status'),
            'count' => $orders->count(),
            'order_ids' => $orders->pluck('id')->values()->all(),
            'order_numbers' => $orders->pluck('order_number')->values()->all(),
            'customer_order_ids' => $orders->where('user_id', $user->id)->pluck('id')->values()->all(),
            'snapshot_order_ids' => $orders
                ->filter(fn (FoodOrder $order) => (int) $order->user_id !== (int) $user->id)
                ->pluck('id')
                ->values()
                ->all(),
            'driver_order_ids' => $orders->where('driver_id', $user->id)->pluck('id')->values()->all(),
        ]);

        return $this->addSuccessResponse(200, 'User food orders fetched successfully', $orders);
    }

    public function listDriverAvailableOrders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius_km' => 'nullable|numeric|min:1|max:30',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $driver = $this->resolveApiUser($request);
        if (! $driver) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), null);
        }
        if (strtolower((string) $driver->user_type) !== 'driver') {
            Log::warning('Food available orders denied: non-driver user', [
                'user_id' => $driver->id,
                'user_type' => $driver->user_type,
            ]);
            return $this->addErrorResponse(403, 'Only driver can access available food orders', null);
        }

        $orders = FoodOrder::query()
            ->with(['restaurant:id,name,logo_image', 'branch:id,restaurant_id,name,address,latitude,longitude', 'customer:id,first_name,last_name,phone,phone_country'])
            ->where('payment_status', 'paid')
            ->where(function ($query) use ($driver) {
                $query->where(function ($q) {
                    $q->whereIn('status', [
                            FoodOrder::STATUS_ACCEPTED,
                            FoodOrder::STATUS_READY_FOR_PICKUP,
                        ])
                        ->whereNull('driver_id');
                })->orWhere(function ($q) use ($driver) {
                    $q->where('driver_id', $driver->id)
                        ->whereIn('status', [
                            FoodOrder::STATUS_ACCEPTED,
                            FoodOrder::STATUS_PREPARING,
                            FoodOrder::STATUS_READY_FOR_PICKUP,
                            FoodOrder::STATUS_PICKED_UP,
                            FoodOrder::STATUS_ON_THE_WAY,
                        ]);
                });
            })
            ->latest('id')
            ->get();
        $orders->each(fn (FoodOrder $order) => $this->hideDeliveryOtpUnlessCustomer($order, $driver));

        Log::info('Food driver available orders response', [
            'driver_id' => $driver->id,
            'driver_user_type' => $driver->user_type,
            'count' => $orders->count(),
            'order_ids' => $orders->pluck('id')->values()->all(),
            'restaurant_accepted_unassigned_ids' => $orders
                ->where('status', FoodOrder::STATUS_ACCEPTED)
                ->whereNull('driver_id')
                ->pluck('id')
                ->values()
                ->all(),
        ]);

        return $this->addSuccessResponse(200, 'Driver food orders fetched successfully', $orders);
    }

    public function assignDriver(Request $request, int $orderId)
    {
        $validator = Validator::make($request->all(), [
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $driver = $this->resolveApiUser($request);
        if (! $driver) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), null);
        }
        if (strtolower((string) $driver->user_type) !== 'driver') {
            return $this->addErrorResponse(403, 'Only driver can accept food order', null);
        }

        $failure = null;
        $order = DB::transaction(function () use ($orderId, $driver, &$failure) {
            $lockedOrder = FoodOrder::where('id', $orderId)->lockForUpdate()->first();
            if (! $lockedOrder) {
                $failure = [404, 'Food order not found'];

                return null;
            }

            if (! empty($lockedOrder->driver_id)) {
                $failure = [409, 'Food order already assigned'];

                Log::warning('Duplicate food order assignment blocked', [
                    'order_id' => $lockedOrder->id,
                    'existing_driver_id' => $lockedOrder->driver_id,
                    'attempted_driver_id' => $driver->id,
                    'status' => $lockedOrder->status,
                ]);

                return null;
            }

            if (! in_array($lockedOrder->status, [FoodOrder::STATUS_ACCEPTED, FoodOrder::STATUS_READY_FOR_PICKUP], true)) {
                $failure = [409, 'Only restaurant accepted or ready food order can be accepted'];

                return null;
            }

            $oldStatus = $lockedOrder->status;
            $lockedOrder->driver_id = $driver->id;
            $lockedOrder->save();

            FoodOrderStatusLog::create([
                'food_order_id' => $lockedOrder->id,
                'from_status' => $oldStatus,
                'to_status' => $oldStatus,
                'changed_by_user_id' => $driver->id,
                'note' => $oldStatus === FoodOrder::STATUS_READY_FOR_PICKUP
                    ? 'Ready order assigned to driver'
                    : 'Restaurant accepted order assigned to driver',
            ]);

            return $lockedOrder;
        });

        if ($failure) {
            return $this->addErrorResponse($failure[0], $failure[1], null);
        }

        $this->notifyFoodOrderAcceptedByDriver($order, $driver);

        $order->load([
            'restaurant',
            'branch',
            'customer:id,first_name,last_name,phone,phone_country',
            'driver:id,first_name,last_name,phone,phone_country',
        ]);
        $this->hideDeliveryOtpUnlessCustomer($order, $driver);

        return $this->addSuccessResponse(200, 'Food order assigned to driver successfully', $order);
    }

    public function updateOrderStatus(Request $request, int $orderId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
            'note' => 'nullable|string|max:500',
            'delivery_otp' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $actor = $this->resolveApiUser($request);
        if (! $actor) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), null);
        }

        $order = FoodOrder::find($orderId);
        if (! $order) {
            return $this->addErrorResponse(404, 'Food order not found', null);
        }

        $newStatus = trim((string) $request->input('status'));
        $oldStatus = $order->status;
        $allowed = $this->allowedNextStatuses($oldStatus);
        if (! in_array($newStatus, $allowed, true)) {
            return $this->addErrorResponse(422, "Invalid status transition from {$oldStatus} to {$newStatus}", null);
        }

        if (strtolower((string) $actor->user_type) === 'driver') {
            if ((string) $order->driver_id !== (string) $actor->id) {
                return $this->addErrorResponse(403, 'This food order is not assigned to this driver', null);
            }

            $driverAllowed = [
                FoodOrder::STATUS_READY_FOR_PICKUP => [FoodOrder::STATUS_PICKED_UP],
                FoodOrder::STATUS_PICKED_UP => [FoodOrder::STATUS_ON_THE_WAY],
                FoodOrder::STATUS_ON_THE_WAY => [FoodOrder::STATUS_DELIVERED],
            ];
            if (! in_array($newStatus, $driverAllowed[$oldStatus] ?? [], true)) {
                return $this->addErrorResponse(422, "Driver cannot transition food order from {$oldStatus} to {$newStatus}", null);
            }

            if ($newStatus === FoodOrder::STATUS_DELIVERED) {
                $inputOtp = preg_replace('/\D+/', '', (string) $request->input('delivery_otp', ''));
                if ($inputOtp === '') {
                    return $this->addErrorResponse(422, 'Customer delivery OTP is required to complete this food order', null);
                }
                if ((string) $order->delivery_otp === '' || ! hash_equals((string) $order->delivery_otp, $inputOtp)) {
                    return $this->addErrorResponse(422, 'Invalid customer delivery OTP', null);
                }
            }
        }

        DB::transaction(function () use ($order, $newStatus, $oldStatus, $actor, $request) {
            $order->status = $newStatus;
            if ($newStatus === FoodOrder::STATUS_PICKED_UP) {
                $order->picked_up_at = now();
            }
            if ($newStatus === FoodOrder::STATUS_DELIVERED) {
                $order->delivered_at = now();
                if ($order->payment_status === 'pending' && $order->payment_method === 'cash_on_delivery') {
                    $order->payment_status = 'paid';
                }
            }
            $order->save();

            if ($newStatus === FoodOrder::STATUS_DELIVERED) {
                $this->creditRestaurantOwnerWallet($order);
                $this->creditFoodDriverWallet($order);
            }

            FoodOrderStatusLog::create([
                'food_order_id' => $order->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by_user_id' => $actor->id,
                'note' => $request->input('note'),
            ]);
        });

        $this->notifyFoodOrderStatusUpdated($order, $oldStatus, $newStatus, $actor);
        $this->hideDeliveryOtpUnlessCustomer($order, $actor);

        return $this->addSuccessResponse(200, 'Food order status updated successfully', $order);
    }

    public function rejectDriverOffer(Request $request, int $orderId)
    {
        $driver = $this->resolveApiUser($request);
        if (! $driver) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), null);
        }
        if (strtolower((string) $driver->user_type) !== 'driver') {
            return $this->addErrorResponse(403, 'Only driver can reject food order offer', null);
        }

        $order = FoodOrder::find($orderId);
        if (! $order) {
            return $this->addErrorResponse(404, 'Food order not found', null);
        }

        if ($order->status !== FoodOrder::STATUS_PLACED || ! empty($order->driver_id)) {
            return $this->addErrorResponse(409, 'Food order is no longer available for reject', null);
        }

        FoodOrderStatusLog::create([
            'food_order_id' => $order->id,
            'from_status' => $order->status,
            'to_status' => $order->status,
            'changed_by_user_id' => $driver->id,
            'note' => 'Offer rejected by driver',
        ]);

        Log::info('Food order offer rejected by driver', [
            'order_id' => $order->id,
            'driver_id' => $driver->id,
        ]);

        return $this->addSuccessResponse(200, 'Food order offer rejected', ['order_id' => $order->id]);
    }

    public function orderStatusTimeline(Request $request, int $orderId)
    {
        $validator = Validator::make($request->all(), [
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = $this->resolveApiUser($request);
        if (! $user) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), null);
        }

        $order = FoodOrder::find($orderId);
        if (! $order) {
            return $this->addErrorResponse(404, 'Food order not found', null);
        }

        if (
            (string) $order->user_id !== (string) $user->id
            && (string) $order->driver_id !== (string) $user->id
        ) {
            return $this->addErrorResponse(403, 'Unauthorized access to this food order timeline', null);
        }

        $timeline = $order->statusLogs()->with('changedBy:id,first_name,last_name,user_type')->orderBy('id')->get();

        return $this->addSuccessResponse(200, 'Food order status timeline fetched successfully', $timeline);
    }

    public function storeOrderLocation(Request $request, int $orderId)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'heading' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
            'recorded_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $driver = $this->resolveApiUser($request);
        if (! $driver) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), null);
        }
        if (strtolower((string) $driver->user_type) !== 'driver') {
            return $this->addErrorResponse(403, 'Only assigned driver can update food order location', null);
        }

        $order = FoodOrder::find($orderId);
        if (! $order) {
            return $this->addErrorResponse(404, 'Food order not found', null);
        }
        if ((string) $order->driver_id !== (string) $driver->id) {
            return $this->addErrorResponse(403, 'This food order is not assigned to this driver', null);
        }
        if ($order->status !== FoodOrder::STATUS_ON_THE_WAY) {
            return $this->addErrorResponse(409, 'Food order live tracking is not active for this status', null);
        }

        $track = FoodOrderLocationTrack::create([
            'food_order_id' => $order->id,
            'driver_id' => $driver->id,
            'latitude' => (float) $request->input('latitude'),
            'longitude' => (float) $request->input('longitude'),
            'heading' => $request->filled('heading') ? (float) $request->input('heading') : null,
            'speed' => $request->filled('speed') ? (float) $request->input('speed') : null,
            'accuracy' => $request->filled('accuracy') ? (float) $request->input('accuracy') : null,
            'recorded_at' => $request->filled('recorded_at') ? $request->input('recorded_at') : now(),
        ]);

        return $this->addSuccessResponse(200, 'Food order location updated successfully', $track);
    }

    public function orderTracking(Request $request, int $orderId)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1|max:500',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = $this->resolveApiUser($request);
        if (! $user) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), null);
        }

        $order = FoodOrder::query()
            ->with([
                'restaurant:id,name,owner_id',
                'branch:id,name,address,latitude,longitude',
                'driver:id,first_name,last_name,phone,phone_country',
            ])
            ->find($orderId);

        if (! $order) {
            return $this->addErrorResponse(404, 'Food order not found', null);
        }

        $canView = (string) $order->user_id === (string) $user->id
            || (string) $order->driver_id === (string) $user->id
            || (string) optional($order->restaurant)->owner_id === (string) $user->id;

        if (! $canView) {
            return $this->addErrorResponse(403, 'Unauthorized access to this food order tracking', null);
        }

        $this->hideDeliveryOtpUnlessCustomer($order, $user);

        $tracks = $order->locationTracks()
            ->orderByDesc('id')
            ->get()
            ->reverse()
            ->values();

        return $this->addSuccessResponse(200, 'Food order tracking fetched successfully', [
            'order' => $order,
            'latest' => $tracks->last(),
            'route_points' => $tracks,
            'firebase_path' => "food_order_tracking/{$order->id}",
            'tracking_active' => in_array($order->status, [
                FoodOrder::STATUS_PICKED_UP,
                FoodOrder::STATUS_ON_THE_WAY,
            ], true),
        ]);
    }

    private function allowedNextStatuses(string $currentStatus): array
    {
        return match ($currentStatus) {
            FoodOrder::STATUS_PLACED => [FoodOrder::STATUS_ACCEPTED, FoodOrder::STATUS_CANCELLED],
            FoodOrder::STATUS_ACCEPTED => [FoodOrder::STATUS_PREPARING, FoodOrder::STATUS_CANCELLED],
            FoodOrder::STATUS_PREPARING => [FoodOrder::STATUS_READY_FOR_PICKUP, FoodOrder::STATUS_CANCELLED],
            FoodOrder::STATUS_READY_FOR_PICKUP => [FoodOrder::STATUS_PICKED_UP, FoodOrder::STATUS_CANCELLED],
            FoodOrder::STATUS_PICKED_UP => [FoodOrder::STATUS_ON_THE_WAY],
            FoodOrder::STATUS_ON_THE_WAY => [FoodOrder::STATUS_DELIVERED],
            default => [],
        };
    }

    private function creditRestaurantOwnerWallet(FoodOrder $order): void
    {
        try {
            $order->loadMissing('restaurant:id,owner_id');
            $ownerId = $order->restaurant?->owner_id;
            if (empty($ownerId)) {
                Log::warning('Food owner wallet credit skipped: restaurant owner missing', [
                    'order_id' => $order->id,
                    'restaurant_id' => $order->restaurant_id,
                ]);

                return;
            }

            $earningAmount = max(0, ((float) $order->items_subtotal - (float) $order->tax_amount) - (float) $order->discount_amount);
            if ($earningAmount <= 0) {
                Log::info('Food owner wallet credit skipped: zero earning amount', [
                    'order_id' => $order->id,
                    'owner_id' => $ownerId,
                ]);

                return;
            }

            $exists = VendorWallet::where('vendor_id', $ownerId)
                ->where('booking_id', $order->id)
                ->where('type', 'credit')
                ->where('description', 'like', 'Restaurant food order earning%')
                ->exists();

            if ($exists) {
                return;
            }

            VendorWallet::create([
                'vendor_id' => $ownerId,
                'booking_id' => $order->id,
                'payout_id' => 0,
                'amount' => round($earningAmount, 2),
                'type' => 'credit',
                'description' => "Restaurant food order earning #{$order->order_number}",
            ]);
        } catch (\Throwable $e) {
            Log::error('Food owner wallet credit failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function creditFoodDriverWallet(FoodOrder $order): void
    {
        if (empty($order->driver_id)) {
            Log::warning('Food driver wallet credit skipped: driver missing', [
                'order_id' => $order->id,
            ]);

            return;
        }

        $lockedOrder = FoodOrder::whereKey($order->id)
            ->lockForUpdate()
            ->first();

        if (! $lockedOrder ||
            $lockedOrder->status !== FoodOrder::STATUS_DELIVERED ||
            $lockedOrder->driver_commission_credited_at !== null) {
            return;
        }

        $commission = max(
            0,
            (float) ($lockedOrder->driver_commission
                ?: $lockedOrder->delivery_fee)
        );

        if ($commission <= 0) {
            Log::warning('Food driver wallet credit skipped: zero commission', [
                'order_id' => $lockedOrder->id,
                'driver_id' => $lockedOrder->driver_id,
            ]);

            return;
        }

        $descriptionPrefix = 'Food delivery earning';
        $alreadyCredited = VendorWallet::where(
            'vendor_id',
            $lockedOrder->driver_id
        )
            ->where('booking_id', $lockedOrder->id)
            ->where('type', 'credit')
            ->where('description', 'like', "{$descriptionPrefix}%")
            ->exists();

        if (! $alreadyCredited) {
            VendorWallet::create([
                'vendor_id' => $lockedOrder->driver_id,
                'booking_id' => $lockedOrder->id,
                'payout_id' => 0,
                'amount' => round($commission, 2),
                'type' => 'credit',
                'description' => "{$descriptionPrefix} #{$lockedOrder->order_number}",
            ]);
        }

        $lockedOrder->driver_commission_credited_at = now();
        $lockedOrder->save();
    }

    private function distanceKm(float $fromLat, float $fromLng, float $toLat, float $toLng): float
    {
        $earthRadiusKm = 6371;
        $latDelta = deg2rad($toLat - $fromLat);
        $lngDelta = deg2rad($toLng - $fromLng);
        $a = sin($latDelta / 2) * sin($latDelta / 2)
            + cos(deg2rad($fromLat)) * cos(deg2rad($toLat))
            * sin($lngDelta / 2) * sin($lngDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusKm * $c, 3);
    }

    private function appendBranchAvailability(RestaurantBranch $branch): void
    {
        $accepting = $this->branchAcceptsOrders($branch);
        $branch->setAttribute('is_accepting_orders', $accepting);
        $branch->setAttribute('effective_is_open', $accepting);
        $branch->setAttribute('availability_label', $this->branchAvailabilityLabel($branch, $accepting));
    }

    private function branchAcceptsOrders(RestaurantBranch $branch): bool
    {
        if (! (bool) $branch->is_active || ! (bool) $branch->is_open) {
            return false;
        }

        $opening = $this->timeToMinutes($branch->opening_time);
        $closing = $this->timeToMinutes($branch->closing_time);
        if ($opening === null || $closing === null || $opening === $closing) {
            return true;
        }

        $now = now(config('app.timezone'))->hour * 60 + now(config('app.timezone'))->minute;
        if ($opening < $closing) {
            return $now >= $opening && $now < $closing;
        }

        return $now >= $opening || $now < $closing;
    }

    private function branchAvailabilityLabel(RestaurantBranch $branch, bool $accepting): string
    {
        if (! (bool) $branch->is_open) {
            return 'Closed manually';
        }

        $opening = $this->formatBranchTime($branch->opening_time);
        $closing = $this->formatBranchTime($branch->closing_time);
        if ($accepting) {
            return $closing ? "Open now • closes {$closing}" : 'Open now';
        }

        return $opening ? "Closed • opens {$opening}" : 'Closed';
    }

    private function timeToMinutes($time): ?int
    {
        $value = trim((string) $time);
        if ($value === '') {
            return null;
        }

        $parts = explode(':', $value);
        if (count($parts) < 2) {
            return null;
        }

        return ((int) $parts[0] * 60) + (int) $parts[1];
    }

    private function formatBranchTime($time): ?string
    {
        $minutes = $this->timeToMinutes($time);
        if ($minutes === null) {
            return null;
        }

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    public function notifyFoodOrderPlaced(FoodOrder $order): void
    {
        try {
            $order->loadMissing([
                'restaurant:id,name,owner_id',
                'customer:id,first_name,last_name,email,phone,phone_country',
            ]);

            $ownerNotified = false;
            $owner = null;
            if ($order->restaurant && ! empty($order->restaurant->owner_id)) {
                $owner = AppUser::find($order->restaurant->owner_id);
            }

            if ($owner && ! empty($owner->fcm)) {
                $customerName = $this->foodOrderCustomerDisplayName($order);
                $subject = 'New Restaurant Order';
                $message = sprintf(
                    '%s placed %s for %s',
                    $customerName !== '' ? $customerName : 'A customer',
                    $order->order_number,
                    $order->restaurant->name ?? 'your restaurant'
                );
                $data = [
                    'route' => 'restaurant_food_order',
                    'food_order_id' => (string) $order->id,
                    'food_order_status' => (string) $order->status,
                    'food_order_number' => (string) $order->order_number,
                    'restaurant_id' => (string) $order->restaurant_id,
                    'customer_name' => $customerName,
                    'customer_phone' => (string) ($order->customer_phone ?: ($order->customer->phone ?? '')),
                    'customer_phone_country' => (string) ($order->customer_phone_country ?: ($order->customer->phone_country ?? '')),
                    'customer_email' => (string) ($order->customer_email ?: ($order->customer->email ?? '')),
                    'total_amount' => (string) $order->total_amount,
                ];
                $ownerNotified = (bool) $this->sendFcmMessage($owner->fcm, $subject, $message, $data, 0, 'restaurant_owner');
            } else {
                Log::warning('Food notify skipped: restaurant owner FCM missing', [
                    'order_id' => $order->id,
                    'restaurant_id' => $order->restaurant_id,
                    'owner_id' => $owner?->id,
                ]);
            }

            Log::info('Food notification: order placed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'owner_id' => $owner?->id,
                'owner_notified' => $ownerNotified,
                'drivers_notified' => 0,
                'driver_dispatch' => 'deferred_until_restaurant_accepts',
            ]);
        } catch (\Throwable $e) {
            Log::error('Food notify failed on placed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifyFoodOrderAcceptedByDriver(FoodOrder $order, AppUser $driver): void
    {
        try {
            $order->loadMissing(['restaurant:id,name,owner_id']);

            $customer = AppUser::find($order->user_id);
            if (! $customer || empty($customer->fcm)) {
                Log::warning('Food notify skipped: customer FCM missing', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ]);
            }

            $subject = 'Food Order Accepted';
            $message = "A driver has been assigned to your order {$order->order_number}.";
            $data = [
                'route' => 'food_order',
                'target_app' => 'user',
                'food_order_id' => (string) $order->id,
                'food_order_status' => (string) $order->status,
                'food_order_number' => (string) $order->order_number,
                'driver_id' => (string) $driver->id,
            ];

            if ($customer && ! empty($customer->fcm)) {
                $customerFcm = trim((string) $customer->fcm);
                $driverFcm = trim((string) $driver->fcm);

                if ($driverFcm !== '' && hash_equals($driverFcm, $customerFcm)) {
                    Log::warning('Food notify skipped: customer FCM matches accepting driver FCM', [
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'driver_id' => $driver->id,
                    ]);
                } else {
                    $this->sendFcmMessage($customerFcm, $subject, $message, $data, 0, 'user');
                }
            }

            $owner = $order->restaurant && $order->restaurant->owner_id
                ? AppUser::find($order->restaurant->owner_id)
                : null;
            if ($owner && ! empty($owner->fcm)) {
                $ownerFcm = trim((string) $owner->fcm);
                $driverFcm = trim((string) $driver->fcm);

                if ($driverFcm !== '' && hash_equals($driverFcm, $ownerFcm)) {
                    Log::warning('Food notify skipped: owner FCM matches accepting driver FCM', [
                        'order_id' => $order->id,
                        'owner_id' => $owner->id,
                        'driver_id' => $driver->id,
                    ]);
                } else {
                    $this->sendFcmMessage(
                        $ownerFcm,
                        'Driver Assigned',
                        "A driver has accepted {$order->order_number}. Please accept and prepare the order.",
                        array_merge($data, [
                            'route' => 'restaurant_food_order',
                            'target_app' => 'restaurant_owner',
                            'restaurant_id' => (string) $order->restaurant_id,
                        ]),
                        0,
                        'restaurant_owner'
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::error('Food notify failed on accept', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifyFoodOrderStatusUpdated(FoodOrder $order, string $oldStatus, string $newStatus, AppUser $actor): void
    {
        try {
            $order->loadMissing(['restaurant:id,name,owner_id']);

            $customer = AppUser::find($order->user_id);
            if ($customer && ! empty($customer->fcm)) {
                $subject = 'Food Order Update';
                $message = "Order {$order->order_number} status: {$newStatus}";
                $data = [
                    'route' => 'food_order',
                    'target_app' => 'user',
                    'food_order_id' => (string) $order->id,
                    'food_order_status' => (string) $newStatus,
                    'food_order_prev_status' => (string) $oldStatus,
                    'food_order_number' => (string) $order->order_number,
                ];
                $this->sendFcmMessage($customer->fcm, $subject, $message, $data, 0, 'user');
            } else {
                Log::warning('Food notify skipped: customer FCM missing on status update', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ]);
            }

            if ($actor->user_type === 'user' && ! empty($order->driver_id)) {
                $driver = AppUser::find($order->driver_id);
                if ($driver && ! empty($driver->fcm)) {
                    $subject = 'Food Order Update';
                    $message = "Order {$order->order_number} status changed to {$newStatus}.";
                    $data = [
                        'route' => 'food_order',
                        'target_app' => 'driver',
                        'food_order_id' => (string) $order->id,
                        'food_order_status' => (string) $newStatus,
                        'food_order_prev_status' => (string) $oldStatus,
                        'food_order_number' => (string) $order->order_number,
                    ];
                    $this->sendFcmMessage($driver->fcm, $subject, $message, $data, 0, 'driver');
                }
            }

            $owner = $order->restaurant && $order->restaurant->owner_id
                ? AppUser::find($order->restaurant->owner_id)
                : null;
            if ($owner && ! empty($owner->fcm) && strtolower((string) $actor->user_type) === 'driver') {
                $data = [
                    'route' => 'restaurant_food_order',
                    'target_app' => 'restaurant_owner',
                    'food_order_id' => (string) $order->id,
                    'food_order_status' => (string) $newStatus,
                    'food_order_prev_status' => (string) $oldStatus,
                    'food_order_number' => (string) $order->order_number,
                    'restaurant_id' => (string) $order->restaurant_id,
                ];
                $this->sendFcmMessage(
                    $owner->fcm,
                    'Food Delivery Update',
                    "Order {$order->order_number} is now {$newStatus}.",
                    $data,
                    0,
                    'restaurant_owner'
                );
            }
        } catch (\Throwable $e) {
            Log::error('Food notify failed on status update', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function resolveApiUser(Request $request): ?AppUser
    {
        $token = (string) $request->input('token', '');
        if ($token === '') {
            $token = (string) $request->header('x-auth-token', '');
        }
        if ($token !== '') {
            $tokenUser = AppUser::where('token', $token)->first();
            if ($tokenUser) {
                return $tokenUser;
            }
        }

        if ($request->user() instanceof AppUser) {
            return $request->user();
        }

        return null;
    }

    private function customerSnapshotName(Request $request, AppUser $user): string
    {
        $requestName = trim((string) $request->input('customer_name', ''));
        if ($requestName !== '') {
            return $requestName;
        }

        $profileName = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
        if ($profileName !== '') {
            return $profileName;
        }

        $email = trim((string) $request->input('customer_email', $user->email ?? ''));
        if ($email !== '') {
            return $email;
        }

        $phone = trim((string) $request->input('customer_phone', $user->phone ?? ''));
        return $phone !== '' ? $phone : 'Customer';
    }

    private function foodOrderCustomerDisplayName(FoodOrder $order): string
    {
        $snapshotName = trim((string) $order->customer_name);
        if ($snapshotName !== '') {
            return $snapshotName;
        }

        $profileName = trim(($order->customer->first_name ?? '').' '.($order->customer->last_name ?? ''));
        if ($profileName !== '') {
            return $profileName;
        }

        $email = trim((string) ($order->customer_email ?: ($order->customer->email ?? '')));
        if ($email !== '') {
            return $email;
        }

        $phone = trim((string) ($order->customer_phone ?: ($order->customer->phone ?? '')));
        return $phone !== '' ? $phone : 'Customer';
    }

    private function createFoodDeliveryOtp(): string
    {
        return (string) random_int(1000, 9999);
    }

    private function hideDeliveryOtpUnlessCustomer(FoodOrder $order, AppUser $user): void
    {
        if ((string) $order->user_id !== (string) $user->id) {
            $order->makeHidden(['delivery_otp']);
        }
    }
}
