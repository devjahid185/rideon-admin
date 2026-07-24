<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Notification extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'ride_notifications';

    protected $fillable = [
        'ride_id',
        'recipient_id', // driver or user ID
        'subject',
        'message',
        'status', // sent, failed
        'sent_at',
    ];

    public $timestamps = false;
}
