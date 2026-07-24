<?php

namespace App\Models\Modern;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'contact_phone',
        'address',
        'latitude',
        'longitude',
        'delivery_radius_km',
        'min_delivery_time_minutes',
        'max_delivery_time_minutes',
        'opening_time',
        'closing_time',
        'is_open',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'delivery_radius_km' => 'float',
        'is_open' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
