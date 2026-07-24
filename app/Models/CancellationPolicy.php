<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancellationPolicy extends Model
{
    public $table = 'booking_cancellation_policies';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'description',
        'type',
        'value',
        'status',
        'module',
        'cancellation_time',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'type' => 'string', // Though it's an ENUM, Eloquent doesn't have direct ENUM support, so we'll manage it as a string
        'value' => 'float',
        'status' => 'boolean', // Assuming that the tinyint(1) type is for boolean-like behavior
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
