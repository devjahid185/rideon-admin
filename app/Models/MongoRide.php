<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoRide extends Model
{
    // Use MongoDB connection
    protected $connection = 'mongodb';

    // The collection name in MongoDB
    protected $collection = 'rides';

    // Fillable fields
    protected $fillable = [
        'user_id',
        'pickup_location',
        'drop_location',
        'status',
        'requested_at',
    ];
}
