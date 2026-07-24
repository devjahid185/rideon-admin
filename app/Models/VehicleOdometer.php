<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleOdometer extends Model
{
    use HasFactory;

    protected $table = 'vehicle_odometer';

    protected $fillable = [
        'name',
        'status',
        'module',
    ];
}
