<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\NotificationTrait;
use App\Http\Controllers\Traits\PaymentStatusUpdaterTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\AppUser;
use App\Models\Booking;
use App\Models\Modern\Item;
use App\Models\Review;
use Illuminate\Http\Request;
use Validator;

class ReviewApiController extends Controller
{
    use MediaUploadingTrait, MiscellaneousTrait, NotificationTrait, PaymentStatusUpdaterTrait, ResponseTrait;

    public function getItemReviews(Request $request)
    {
        try {

            $itemId = $request->input('item_id');
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $item = Item::findOrFail($itemId);

            $reviews = Review::where('item_id', $itemId)
                ->orderByDesc('created_at')
                ->skip($offset)
                ->take($limit)
                ->get();

            $reviewData = [];
            foreach ($reviews as $review) {

                $guest = AppUser::where('id', $review->guestid)->first();
                $guestImageUrl = null;
                if ($guest && $guest->profile_image) {
                    $guestImageUrl = $guest->profile_image->url;
                }
                $reviewData[] = [
                    'id' => $review->id,
                    'booking_id' => $review->bookingid,
                    'item_id' => $review->item_id,
                    'item_name' => $review->item_name,
                    'guest_id' => $review->guestid,
                    'guest_name' => $review->guest_name,
                    'guest_image' => $guestImageUrl, // Add guest profile image URL
                    'rating' => $review->guest_rating,
                    'message' => $review->guest_message,
                    'created_at' => $review->created_at->format('F Y'),
                    'updated_at' => $review->updated_at->format('F Y'),
                ];
            }
            $nextoffset = $offset + count($reviews);
            if ($reviews->isEmpty()) {
                $nextoffset = -1;
            }

            return $this->addSuccessResponse(200, trans('global.Result_found'), [
                'reviews' => $reviewData,
                'limit' => $limit,
                'offset' => $nextoffset,

            ]);
        } catch (Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    public function giveReviewByUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|exists:app_users,token',
            'booking_id' => 'required|numeric|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $bookingDetails = Booking::where('id', $request->booking_id)
            ->first();
        $item_id = $bookingDetails->itemid;

        $item_name = Item::where('id', $item_id)->first();
        $hostid = $item_name->userid_id;

        $user = AppUser::where('token', $request->input('token'))->first();
        if (! $user) {
            return $this->addErrorResponse(404, trans('global.user_not_found'), '');
        }

        $guestid = $user->id;

        $booking = Booking::where('id', $request->booking_id)
            ->where('itemid', $item_id)
            ->where('host_id', $hostid)
            ->where('userid', $guestid)
            ->first();

        if (! $booking) {
            return $this->addErrorResponse(422, trans('global.You_must_book_the_item_before_giving_a_review'), '');
        }
        $booking->update(['status' => 'Completed']);

        $existingReview = Review::where('bookingid', $request->booking_id)
            ->where('guestid', $guestid)
            ->where('item_id', $item_id)
            ->where(function ($query) {
                $query->where('guest_rating', '>', 0)
                    ->orWhereNotNull('guest_message');
            })
            ->first();

        if ($existingReview) {
            return $this->addErrorResponse(422, trans('global.A_review_already_exists_for_this_booking_guest_item'), '');
        }

        $guest = AppUser::where('id', $guestid)->first();
        $host = AppUser::where('id', $hostid)->first();

        $ratedAddedByUser = Review::where('bookingid', $request->booking_id)
            ->where('guestid', $guestid)
            ->where('item_id', $item_id)->first();

        if ($ratedAddedByUser) {
            $ratedAddedByUser->guest_rating = $request->rating;
            $ratedAddedByUser->guest_message = $request->message;
            $ratedAddedByUser->save();
            $template_id = 13;

            $this->sendReviewNotification($template_id, $ratedAddedByUser, $guestid, $hostid, $item_id, $request->booking_id);
        }

        $review = new Review([
            'bookingid' => $request->booking_id,
            'item_id' => $item_id,
            'item_name' => $item_name->title,
            'guestid' => $guestid,
            'guest_name' => $guest->first_name.' '.$guest->last_name,
            'hostid' => $hostid,
            'host_name' => $host->first_name.' '.$host->last_name,
            'guest_rating' => $request->rating,
            'guest_message' => $request->message,
            'module' => $request->module_id,
        ]);

        $review->save();
        $this->updateItemAverageRating($item_id);
        $this->calculateAverageHostRating($hostid);

        $template_id = 13;
        $this->sendReviewNotification($template_id, $review, $guestid, $hostid, $item_id, $request->booking_id);

        return $this->addSuccessResponse(200, trans('global.Review_created_successfully'), ['review' => $review]);
        try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    public function giveReviewByHost(Request $request)
    {
        // try {
        $validator = Validator::make($request->all(), [
            'token' => 'required|exists:app_users,token',
            'booking_id' => 'required|numeric|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $bookingDetails = Booking::where('id', $request->booking_id)->first();
        $item_id = $bookingDetails->itemid;
        $guestid = $bookingDetails->userid;
        $item_name = Item::where('id', $item_id)->first();

        // Check if the user exists based on the token
        $host = AppUser::where('token', $request->input('token'))->first();
        if (! $host) {
            return $this->addErrorResponse(404, trans('global.user_not_found'), '');
        }
        $hostid = $host->id;

        $booking = Booking::where('id', $request->booking_id)
            ->where('itemid', $item_id)
            ->where('userid', $guestid)
            ->where('host_id', $hostid)
            ->first();

        if (! $booking) {
            return $this->addErrorResponse(422, trans('global.You_can_only_review_bookings_that_belong_to_you'), '');
        }

        $existingReview = Review::where('bookingid', $request->booking_id)
            ->where('guestid', $guestid)
            ->where('item_id', $item_id)
            ->where(function ($query) {
                $query->where('host_rating', '>', 0)
                    ->orWhereNotNull('host_message');
            })
            ->first();

        if ($existingReview) {
            return $this->addErrorResponse(422, trans('global.A_review_already_exists_for_this_user'), '');
        }
        $ratedAddedByUser = Review::where('bookingid', $request->booking_id)
            ->where('guestid', $guestid)
            ->where('item_id', $item_id)->first();

        if ($ratedAddedByUser) {
            $ratedAddedByUser->host_rating = $request->rating;
            $ratedAddedByUser->host_message = $request->message;
            $ratedAddedByUser->save();
            $template_id = 12;

            $this->sendReviewNotification($template_id, $ratedAddedByUser, $guestid, $hostid, $item_id, $request->booking_id);

            return $this->addSuccessResponse(200, trans('global.Review_updated_successfully'), ['review' => $ratedAddedByUser]);
        }

        $guest = AppUser::where('id', $guestid)->first();

        $review = new Review([
            'bookingid' => $request->booking_id,
            'item_id' => $item_id,
            'item_name' => $item_name->title,
            'guestid' => $guestid,
            'guest_name' => $guest->first_name.' '.$guest->last_name,
            'hostid' => $hostid,
            'host_name' => $host->first_name.' '.$host->last_name,
            'host_rating' => $request->rating,
            'host_message' => $request->message,
            'module' => $request->module_id,
        ]);

        $review->save();
        $this->calculateHostRatingForGuest($guestid);

        $template_id = 12;

        $this->sendReviewNotification($template_id, $review, $guestid, $hostid, $item_id, $request->booking_id);

        return $this->addSuccessResponse(200, trans('global.Review_created_successfully'), ['review' => $review]);
        try {
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    private function sendReviewNotification($templateId, $review, $guestid, $hostid, $item_id, $booking_id)
    {
        $valuesArray = [];
        $template_id = $templateId;
        $valuesArray = $this->createNotificationArray($guestid, $hostid, $item_id, $booking_id);
        $dataVal['message_key'] = $review;
        $this->sendAllNotifications($valuesArray, $guestid, $template_id, $dataVal, $hostid);
    }

    public function updateItemAverageRating($itemId)
    {

        $reviews = Review::where('item_id', $itemId)->get();
        $totalRating = 0;
        foreach ($reviews as $review) {
            $totalRating += $review->guest_rating;
        }

        $averageRating = ($reviews->count() > 0) ? $totalRating / $reviews->count() : 0;
        $item = Item::find($itemId);
        if ($item) {
            $item->update(['item_rating' => $averageRating]);
        }
    }

    public function calculateHostRatingForGuest($guestId)
    {
        $reviews = Review::where('guestid', $guestId)->get();
        if ($reviews->isEmpty()) {
            return '';
        }

        $totalHostRating = $reviews->sum('host_rating');
        $averageHostRating = $totalHostRating / $reviews->count();
        $guest = AppUser::find($guestId);
        if ($guest) {
            $guest->update(['avr_guest_rate' => $averageHostRating]);
        }

        return '';
    }

    public function calculateAverageHostRating($hostId)
    {
        $allRatings = Item::where('userid_id', $hostId)->get();
        $totalRating = $allRatings->sum('item_rating');
        $averageRating = $totalRating / $allRatings->count();
        $host = AppUser::find($hostId);
        if ($host) {
            $host->update(['ave_host_rate' => $averageRating]);
        } else {
            return '';
        }

        return '';
    }
}
