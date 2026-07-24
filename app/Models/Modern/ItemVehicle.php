<?php

namespace App\Models\Modern;

use Illuminate\Database\Eloquent\Model;

class ItemVehicle extends Model
{
    protected $table = 'rental_item_extension';

    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'year',
        'color',
        'vehicle_registration_number',
        'odometer',
        'transmission',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
