<?php

namespace App\Models;

use App\Models\Modern\RentalItemServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingExtension extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'is_item_deliver',
        'is_item_received',
        'is_item_returned',
        'doorStep_price',
        'pickup_location',
        'dropoff_location',
        'estimated_distance_km',
        'estimated_duration_min',
        'pick_otp',
        'drop_otp',
        'service_type_id',
        'ride_id',
    ];

    protected $casts = [
        'pickup_location' => 'array',
        'dropoff_location' => 'array',
    ];

    // Relationship to the Booking model
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(RentalItemServiceType::class, 'service_type_id');
    }
}
