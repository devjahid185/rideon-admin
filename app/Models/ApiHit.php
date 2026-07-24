<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ApiHit extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'api_hits';

    protected $fillable = [
        'route',
        'method',
        'user_id',
        'ip',
        'user_agent',
        'meta',
        'requested_at',
    ];
}
