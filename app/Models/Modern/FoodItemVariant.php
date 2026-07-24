<?php

namespace App\Models\Modern;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodItemVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_item_id',
        'name',
        'price_delta',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'price_delta' => 'float',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id');
    }
}

