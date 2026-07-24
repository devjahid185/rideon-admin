<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\EmailTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\NotificationTrait;
use App\Http\Controllers\Traits\PushNotificationTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Http\Controllers\Traits\SMSTrait;
use App\Http\Controllers\Traits\UserWalletTrait;
use App\Http\Controllers\Traits\VendorWalletTrait;
use App\Models\AppUser;
use App\Models\Modern\Item;
use App\Models\Modern\ItemType;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class UserProfileController extends Controller
{
    use EmailTrait, MediaUploadingTrait, MiscellaneousTrait, NotificationTrait, PushNotificationTrait, ResponseTrait, SMSTrait, UserWalletTrait, VendorWalletTrait;

    public function getUserProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(400, trans('global.Validation_Error'));
        }

        $user = AppUser::where('id', $request->input('userid'))->first();
        if (! $user) {
            return $this->errorResponse(404, trans('global.User_Not_Found'));
        }
        $join_in = Carbon::parse($user->created_at)->format('F Y');

        $module = $this->getModuleIdOrDefault($request);
        $items = Item::where('userid_id', $user->id)
            ->where('module', $module)
            ->get();

        $birthdate = Carbon::parse($user->birthdate);
        $currentYear = now()->year;
        $userAge = $currentYear - $birthdate->year;
        if (now()->subYears($userAge) < $birthdate) {
            $userAge--;
        }

        $startedHostingDate = $items->min('created_at');
        $currentDate = now();

        $dateDiff = $startedHostingDate->diff($currentDate);
        $yearsOfHosting = $dateDiff->y;
        $monthsOfHosting = $dateDiff->m;
        $daysOfHosting = $dateDiff->d;

        $hostingDuration = '';
        if ($yearsOfHosting > 0) {
            $hostingDuration = "{$yearsOfHosting} year(s)";
        } elseif ($monthsOfHosting > 0) {
            $hostingDuration = "{$monthsOfHosting} month(s)";
        } else {
            $hostingDuration = "{$daysOfHosting} day(s)";
        }

        $totalGuestReviews = $items->sum(function ($item) {
            return $item->reviews->filter(function ($review) {
                return ! is_null($review->guest_rating);
            })->count();
        });
        $totalGuestRatings = $items->sum(function ($item) {
            return $item->reviews->filter(function ($review) {
                return ! is_null($review->guest_rating);
            })->sum('guest_rating');
        });
        $averageGuestRating = ($totalGuestReviews > 0) ? $totalGuestRatings / $totalGuestReviews : 0;
        $user_profile_image = null;
        if (isset($user->profile_image->preview_url)) {
            $user_profile_image = $user->profile_image->preview_url;
        } else {
            $user_profile_image = null;
        }

        $responseData = [
            'name' => $user->first_name.' '.$user->last_name,
            'profile_image' => $user_profile_image,
            'profile_background' => $user_profile_image,
            'intro_text' => $user->intro ?? '',
            'total_reviews_on_items' => $totalGuestReviews,
            'average_rating_on_items' => $averageGuestRating,
            'years_of_hosting' => $hostingDuration,
            'languages' => $user->languages ?? 'English',
            'livecity' => $user->country ?? '',
            'age' => $userAge.' years',
            'join_in' => $join_in,
            'verified_identity' => $user->verified,
            'verified_email' => $user->email_verify,
            'verified_phone' => $user->phone_verify,

        ];

        return $this->successResponse(200, trans('global.Profile_Retrieved_Successfully'), $responseData);
    }

    public function getUseritems(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'userid' => ['required'],
            'offset' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user = AppUser::where('id', $request->input('userid'))->first();
        if (! $user) {
            return $this->errorResponse(404, trans('global.User_Not_Found'));
        }
        try {

            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $module = $this->getModuleIdOrDefault($request);
            $items = Item::where('userid_id', $user->id)
                ->where('status', 1)
                ->orderBy('views_count', 'desc')
                ->where('module', $module)
                ->offset($offset)
                ->limit($limit)
                ->get()
                ->map(function ($item) use ($user) {
                    $formattedItem = $this->formatItemData($item);
                    if ($user) {
                        $formattedItem['is_in_wishlist'] = $user->itemWishlists()
                            ->where('item_id', $item->id)
                            ->exists();
                    } else {
                        $formattedItem['is_in_wishlist'] = false;
                    }
                    $itemType = ItemType::find($item->item_type_id);

                    if ($itemType) {
                        $formattedItem['item_type'] = $itemType->name;
                    } else {

                        $formattedItem['item_type'] = '';
                    }

                    return $formattedItem;
                });

            $nextOffset = $request->input('offset', 0) + count($items);
            if ($items->isEmpty()) {
                $nextOffset = -1;
            }

            return $this->addSuccessResponse(200, trans('global.Result_found'), [
                'items' => $items,
                'offset' => $nextOffset,
            ]);
        } catch (\Exception $e) {

            return $this->errorResponse(500, trans('global.Internal_Server_Error'));
        }
    }

    public function getVendorItemReviews(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'userid' => ['required'],
            'offset' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(400, 'Validation error.');
        }
        $module = $this->getModuleIdOrDefault($request);
        $user = AppUser::where('id', $request->input('userid'))->first();

        if (! $user) {
            return $this->errorResponse(404, 'User not found.');
        }

        $hostId = $user->id;
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        // try {
        $reviews = Review::where('hostid', $hostId)
            ->offset($offset)
            ->where('module', $module)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($review) {
                $guest = AppUser::where('id', $review->guestid)->first();
                $guest_profile = null;
                $guest_profile = null;

                if ($guest && isset($guest->profile_image->preview_url)) {
                    $guest_profile = $guest->profile_image->preview_url;
                } else {
                    $guest_profile = null;
                }

                $host = AppUser::where('id', $review->hostid)->first();
                $host_profile = null;
                if ($host && isset($host->profile_image->preview_url)) {
                    $host_profile = $host->profile_image->preview_url;
                }

                return [
                    'item_id' => $review->item_id,
                    'item_name' => $review->item_name,
                    'guest_response' => [
                        'guest_name' => $review->guest_name,
                        'guest_rating' => $review->guest_rating,
                        'guest_message' => $review->guest_message,
                        'guest_profile' => $guest_profile,
                        'guest_id' => $review->guestid,
                    ],
                    'host_response' => [
                        'host_name' => $review->host_name,
                        'host_rating' => $review->host_rating,
                        'host_message' => $review->host_message,
                        'host_profile' => $host_profile,
                        'host_id' => $review->hostid,
                    ],
                    'created_at' => $review->created_at->format('F Y'),
                ];
            });
        $nextOffset = $request->input('offset', 0) + count($reviews);
        if ($reviews->isEmpty()) {
            $nextOffset = -1;
        }

        return $this->addSuccessResponse(200, trans('global.Reviews retrieved successfully.'), [
            'reviews' => $reviews,
            'offset' => $nextOffset,
        ]);

        try {
        } catch (\Exception $e) {
            return $this->errorResponse(500, 'Internal Server Error.');
        }
    }
}
