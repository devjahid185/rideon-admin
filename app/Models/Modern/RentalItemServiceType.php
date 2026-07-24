<?php

namespace App\Models\Modern;

use Illuminate\Database\Eloquent\Model;

class RentalItemServiceType extends Model
{
    protected $table = 'rental_item_service_types';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function rentalItemTypes()
    {
        return $this->belongsToMany(
            RentalItemType::class,
            'rental_item_type_service_type',
            'rental_item_service_type_id',
            'rental_item_type_id'
        );
    }
}
