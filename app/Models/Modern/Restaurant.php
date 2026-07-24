<?php

namespace App\Models\Modern;

use App\Models\AppUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'phone',
        'email',
        'description',
        'cover_image',
        'logo_image',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'float',
    ];

    public function branches()
    {
        return $this->hasMany(RestaurantBranch::class);
    }

    public function owner()
    {
        return $this->belongsTo(AppUser::class, 'owner_id');
    }

    public function categories()
    {
        return $this->hasMany(FoodCategory::class);
    }

    public function items()
    {
        return $this->hasMany(FoodItem::class);
    }
}
