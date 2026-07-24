<?php

namespace App\Models\Modern;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodAddon extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_item_id',
        'name',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'is_active' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id');
    }
}

