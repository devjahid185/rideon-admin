<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RideRequest;
use Illuminate\Http\Request;

class RideRequestController extends Controller
{
    // Create a new ride request
    public function createRide(Request $request)
    {
        $ride = RideRequest::create([
            'user_id' => $request->user_id,
            'pickup_location' => $request->pickup_location,
            'drop_location' => $request->drop_location,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return response()->json($ride);
    }

    // Get all ride requests
    public function getRides()
    {
        $rides = RideRequest::all();

        return response()->json($rides);
    }
}
