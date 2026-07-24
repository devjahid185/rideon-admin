<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ResponseTrait;
use App\Models\RideRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class RideRequestController extends Controller
{
    use ResponseTrait;

    /**
     * Create a new ride request in MongoDB.
     */
    public function createRide(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'pickup_location' => 'required|string',
            'drop_location' => 'required|string',
        ]);

        // Insert into MongoDB
        $ride = RideRequest::create(array_merge($validated, [
            'status' => 'pending',
            'requested_at' => now(),
        ]));

        return response()->json([
            'success' => true,
            'data' => $ride,
        ]);
    }

    /**
     * Get all ride requests.
     */
    public function getRides()
    {
        // Get start and end of today
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();

        // Fetch rides from MongoDB where requested_at is today
        $rides = RideRequest::whereBetween('requested_at', [$startOfDay, $endOfDay])->get();

        return response()->json([
            'success' => true,
            'data' => $rides,
        ]);
    }

    public function updateRideStatus(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,accepted,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(400, trans('global.Validation_Error'));
        }

        // Get validated data
        $validated = $validator->validated();

        // Find the ride by ID
        $ride = RideRequest::find($id);

        if (! $ride) {
            return response()->json([
                'success' => false,
                'message' => 'Ride not found',
            ], 404);
        }

        // Update status
        $ride->status = $validated['status'];
        $ride->save();

        return response()->json([
            'success' => true,
            'data' => $ride,
            'message' => 'Ride status updated successfully',
        ]);
    }
}
