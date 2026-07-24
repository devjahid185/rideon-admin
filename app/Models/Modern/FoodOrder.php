<?php

namespace App\Models\Modern;

use App\Models\AppUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodOrder extends Model
{
    use HasFactory;

    public const STATUS_PAYMENT_PENDING = 'payment_pending';
    public const STATUS_PLACED = 'placed';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY_FOR_PICKUP = 'ready_for_pickup';
    public const STATUS_PICKED_UP = 'picked_up';
    public const STATUS_ON_THE_WAY = 'on_the_way';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_number',
        'user_id',
        'restaurant_id',
        'restaurant_branch_id',
        'driver_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_phone_country',
        'delivery_otp',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'items_subtotal',
        'tax_amount',
        'delivery_fee',
        'driver_commission',
        'driver_commission_credited_at',
        'platform_fee',
        'discount_amount',
        'total_amount',
        'customer_note',
        'placed_at',
        'accepted_at',
        'picked_up_at',
        'delivered_at',
    ];

    protected $casts = [
        'delivery_latitude' => 'float',
        'delivery_longitude' => 'float',
        'items_subtotal' => 'float',
        'tax_amount' => 'float',
        'delivery_fee' => 'float',
        'driver_commission' => 'float',
        'driver_commission_credited_at' => 'datetime',
        'platform_fee' => 'float',
        'discount_amount' => 'float',
        'total_amount' => 'float',
        'placed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(AppUser::class, 'user_id');
    }

    public function driver()
    {
        return $this->belongsTo(AppUser::class, 'driver_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function branch()
    {
        return $this->belongsTo(RestaurantBranch::class, 'restaurant_branch_id');
    }

    public function items()
    {
        return $this->hasMany(FoodOrderItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(FoodOrderStatusLog::class);
    }

    public function locationTracks()
    {
        return $this->hasMany(FoodOrderLocationTrack::class);
    }
}
