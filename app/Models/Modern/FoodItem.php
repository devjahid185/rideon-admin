<?php

namespace App\Models\Modern;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'food_category_id',
        'name',
        'description',
        'base_price',
        'image',
        'is_veg',
        'is_available',
    ];

    protected $casts = [
        'base_price' => 'float',
        'is_veg' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(FoodCategory::class, 'food_category_id');
    }

    public function variants()
    {
        return $this->hasMany(FoodItemVariant::class);
    }

    public function addons()
    {
        return $this->hasMany(FoodAddon::class);
    }
}

