<?php

namespace App\Models\Modern;

use Illuminate\Database\Eloquent\Model;

class RentalItemTypeServiceType extends Model
{
    protected $table = 'rental_item_type_service_type';

    protected $fillable = [
        'rental_item_type_id',
        'rental_item_service_type_id',
    ];
}
