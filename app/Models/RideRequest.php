<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model; // Official MongoDB Laravel driver

class RideRequest extends Model
{
    // Use the MongoDB connection
    protected $connection = 'mongodb';

    // MongoDB collection name
    protected $collection = 'rides';

    // Fillable fields for mass assignment
    protected $fillable = [
        'user_id',
        'pickup_location',
        'drop_location',
        'status',
        'requested_at',
    ];

    // Optional: disable timestamps if your collection doesn't have created_at / updated_at
    public $timestamps = false;
}
