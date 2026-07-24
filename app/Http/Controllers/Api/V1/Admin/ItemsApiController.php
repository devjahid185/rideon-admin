<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\BookingAvailableTrait;
use App\Http\Controllers\Traits\FirestoreTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Controllers\Traits\MiscellaneousTrait;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\AppUser;
use App\Models\Modern\Item;
use App\Models\Modern\ItemVehicle;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Validator;

class ItemsApiController extends Controller
{
    use BookingAvailableTrait, FirestoreTrait, MediaUploadingTrait, MiscellaneousTrait, ResponseTrait;

    public function editItem(Request $request)
    {

        $data = json_encode($request->all())."\n";
        $validator = Validator::make($request->all(), [
            'token' => 'required|exists:app_users,token',
            'item_type_id' => 'required|exists:rental_item_types,id',
            'item_rating' => 'nullable|numeric',
            'status' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'state_region' => 'nullable|string|max:255',
            'zip_postal_code' => 'nullable|string|max:255',
            'platitude' => 'nullable|string|max:255',
            'plongitude' => 'nullable|string|max:255',
            'is_verified' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',

        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }
        // try {

        $user_id = $this->checkUserByToken($request->token);
        if (! $user_id) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), '');
        }

        $item = Item::where('id', $request->input('id'))->where('userid_id', $user_id)->first();
        $item->update([
            'item_type_id' => $request->item_type_id,
            'status' => $item->status,
            'place_id' => $request->place_id,
            'latitude' => $request->platitude,
            'longitude' => $request->plongitude,
            'make' => $request->make,
            'model' => $request->model,
            'registration_number' => $request->registration_number,

        ]);
        ItemVehicle::updateOrCreate(
            ['item_id' => $item->id],
            [
                'year' => $request->year,
                'color' => $request->color,
            ]
        );
        $userData = AppUser::find($user_id);

        if (! $userData->firestore_id) {
            $firestoreData = $this->generateDriverFirestoreData($userData);
            $firestoreDoc = $this->storeDriverInFirestore($firestoreData);
            $firestoreDocId = basename($firestoreDoc);

            $userData->update([
                'firestore_id' => $firestoreDocId,
                'user_type' => 'driver',
            ]);

            $user['firestore_id'] = $firestoreDocId;
        }

        $firestoreData = $this->generateDriverFirestoreData($userData);
        $documentId = $userData->firestore_id;
        $this->updateDocument('drivers', $documentId, $firestoreData);

        return $this->addSuccessResponse(200, trans('global.item_updated_successfully'), $item);

        try {
        } catch (ModelNotFoundException $e) {
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        } catch (\Exception $e) {
            // update, return a generic error response
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    public function myItems(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|exists:app_users,token',
            ]);

            if ($validator->fails()) {
                return $this->errorComputing($validator);
            }

            $limit = (int) $request->input('limit', 10);
            $offset = (int) $request->input('offset', 0);
            $search = $request->input('search');

            $user = AppUser::select('id', 'host_status')->where('token', $request->token)->first();

            if (! $user) {
                return $this->addErrorResponse(419, trans('front.token_not_match'), '');
            }

            $user_id = $user->id;
            $hostStatus = $user->host_status;
            if (! $user_id) {
                return $this->addErrorResponse(419, trans('front.token_not_match'), '');
            }

            $module = $this->getModuleIdOrDefault($request);
            $checkLimit = 1;
            $itemsQuery = Item::with(['item_type', 'itemVehicle'])
                ->where('userid_id', $user_id)
                ->where('module', $module);
            if (! empty($search)) {
                $itemsQuery->where('title', 'like', "%{$search}%");
            }
            $items = $itemsQuery->skip($offset)->take($limit)->get();
            $formattedItems = $items->map(function ($item) {
                $item['item_type'] = $item->itemType->name ?? '';
                $latitude = $item->latitude;
                $longitude = $item->longitude;
                unset($item->latitude);
                unset($item->longitude);
                $item['year'] = $item->itemVehicle->year ?? null;
                $item['color'] = $item->itemVehicle->color ?? null;
                $item['latitude'] = $latitude;
                $item['longitude'] = $longitude;

                return $item;
            });

            $nextOffset = $formattedItems->isNotEmpty() ? $offset + $formattedItems->count() : -1;
            $responseData = [
                'items' => $formattedItems,
                'offset' => $nextOffset,
                'limit' => $limit,
                'host_status' => $hostStatus,
                'checkLimit' => $checkLimit,
            ];

            return $this->addSuccessResponse(200, trans('front.item_found'), $responseData);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, trans('front.something_wrong'), $e->getMessage());
        }
    }

    public function addEditItemImage(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:rental_items,id',
            'token' => 'required|exists:app_users,token',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user_id = $this->checkUserByToken($request->token);
        if (! $user_id) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), '');
        }
        try {
            $item = Item::where('id', $request->input('id'))->where('userid_id', $user_id)->first();
            $steps = json_decode($item->steps_completed, true) ?: [];

            // Define progress increments

            $photoIncrement = 0;
            $documentIncrement = 0;

            if ($request->input('front_image')) {

                if ($item->hasMedia('front_image')) {

                    $item->getFirstMedia('front_image')->delete();
                }

                $frontImage = $request->input('front_image');
                $frontImageUrl = $this->serveBase64Image($frontImage);
                $item->addMedia($frontImageUrl)->toMediaCollection('front_image');

                if (isset($steps['photos']) && $steps['photos'] === false) {
                    $steps['photos'] = true;
                    $photoIncrement = 11.11;
                }
            }
            $gallery_image_delete = explode(',', str_replace(']', '', str_replace('[', '', $request->input('gallery_image_delete'))));

            if (! empty($gallery_image_delete)) {
                foreach ($gallery_image_delete as $val) {
                    $url = $val;
                    $fileName = basename($url);

                    // $media = Media::where('file_name', 'LIKE', '%' . $fileName . '%')
                    //     ->where('model_id', $request->input('id'))
                    //     ->first();
                    $media = Media::where('file_name', $fileName)
                        ->where('model_id', $request->input('id')) // Ensure the model_id matches the given ID
                        ->first();
                    if ($media) {
                        $media->delete();
                    }
                }
            }
            if ($request->input('gallery_image')) {
                $gallery_images = explode('##', $request->input('gallery_image'));
                foreach ($gallery_images as $galleryImage) {
                    $gallaeryImageUrl[] = $this->serveBase64Image($galleryImage);
                }
                foreach ($gallaeryImageUrl as $url) {

                    $item->addMedia($url)->toMediaCollection('gallery');
                }
            }
            if ($request->has('vehicle_registration_doc') && $request->input('vehicle_registration_doc')) {

                if ($item->hasMedia('vehicle_registration_doc')) {
                    $item->getFirstMedia('vehicle_registration_doc')->delete();
                }

                $frontImageDoc = $request->input('vehicle_registration_doc');
                $frontImageDocUrl = $this->serveBase64Image($frontImageDoc);
                $item->addMedia($frontImageDocUrl)->toMediaCollection('vehicle_registration_doc');

                if (isset($steps['document']) && $steps['document'] === false) {
                    $steps['document'] = true;
                    $documentIncrement = 11.11;
                }
            }

            if ($photoIncrement > 0 || $documentIncrement > 0) {
                if (isset($steps['photos']) && $steps['photos']) {
                    if ($item->step_progress < 100) {
                        $item->step_progress += $photoIncrement;
                    }
                }
                if (isset($steps['document']) && $steps['document']) {
                    if ($item->step_progress < 100) {
                        $item->step_progress += $documentIncrement;
                    }
                }

                // Ensure step_progress does not exceed 100
                if ($item->step_progress > 100) {
                    $item->step_progress = 100;
                }

                $item->steps_completed = json_encode($steps);
                $item->save();
            }

            $itemMetaInfo = $this->getModuleInfoValues($request->module_id, $item->id);
            $data = [
                'itemMetaInfo' => $itemMetaInfo ?? null,
            ];
            $this->addOrUpdateItemMeta($item->id, $data);

            return $this->addSuccessResponse(200, trans('global.images_added_successfully'), $item);
        } catch (\Exception $e) {
            // update, return a generic error response
            return $this->addErrorResponse(500, trans('global.something_wrong'), $e->getMessage());
        }
    }

    public function addEditItemImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:rental_items,id',
            'token' => 'required|exists:app_users,token',
        ]);

        if ($validator->fails()) {
            return $this->errorComputing($validator);
        }

        $user_id = $this->checkUserByToken($request->token);
        if (! $user_id) {
            return $this->addErrorResponse(419, trans('global.token_not_match'), '');
        }

        try {
            $item = Item::where('id', $request->input('id'))->where('userid_id', $user_id)->first();

            if (! $item) {
                return $this->addErrorResponse(404, trans('global.item_not_found'), '');
            }

            // FRONT IMAGE
            if ($request->input('front_image')) {
                if ($item->hasMedia('front_image')) {
                    $item->getFirstMedia('front_image')->delete();
                }
                $frontImage = $request->input('front_image');
                $frontImageUrl = $this->serveBase64Image($frontImage);
                $item->addMedia($frontImageUrl)->toMediaCollection('front_image');
            }

            // FRONT IMAGE DOC
            if ($request->has('vehicle_registration_doc') && $request->input('vehicle_registration_doc')) {
                if ($item->hasMedia('vehicle_registration_doc')) {
                    $item->getFirstMedia('vehicle_registration_doc')->delete();
                }
                $frontImageDoc = $request->input('vehicle_registration_doc');
                $frontImageDocUrl = $this->serveBase64Image($frontImageDoc);
                $item->addMedia($frontImageDocUrl)->toMediaCollection('vehicle_registration_doc');
            }

            // INSURANCE DOC
            if ($request->has('vehicle_insurance_doc') && $request->input('vehicle_insurance_doc')) {
                if ($item->hasMedia('vehicle_insurance_doc')) {
                    $item->getFirstMedia('vehicle_insurance_doc')->delete();
                }
                $insuranceDoc = $request->input('vehicle_insurance_doc');
                $insuranceDocUrl = $this->serveBase64Image($insuranceDoc);
                $item->addMedia($insuranceDocUrl)->toMediaCollection('vehicle_insurance_doc');
            }

            return $this->addSuccessResponse(200, trans('global.images_added_successfully'), $item);
        } catch (\Exception $e) {
            return $this->addErrorResponse(500, $e->getMessage(), $e->getMessage());
        }
    }
}
