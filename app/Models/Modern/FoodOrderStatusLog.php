<?php

namespace App\Models\Modern;

use App\Models\AppUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodOrderStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_order_id',
        'from_status',
        'to_status',
        'changed_by_user_id',
        'note',
    ];

    public function order()
    {
        return $this->belongsTo(FoodOrder::class, 'food_order_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(AppUser::class, 'changed_by_user_id');
    }
}

