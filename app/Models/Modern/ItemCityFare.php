<?php

namespace App\Models\Modern;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCityFare extends Model
{
    use HasFactory;

    protected $table = 'item_city_fare';

    protected $fillable = [
        'item_type_id',
        'min_fare',
        'max_fare',
        'recommended_fare',
        'admin_commission',
        'fare_per_minute',
    ];

    /**
     * Relationship: belongs to ItemType.
     */
    public function itemType()
    {
        return $this->belongsTo(ItemType::class, 'item_type_id');
    }
}
