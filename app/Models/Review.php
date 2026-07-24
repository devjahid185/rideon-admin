<?php

namespace App\Models;

use App\Models\Modern\Item;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public $table = 'reviews';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'bookingid',
        'item_id',
        'item_name',
        'guestid',
        'guest_name',
        'hostid',
        'host_name',
        'guest_rating',
        'host_rating',
        'guest_message',
        'host_message',
        'created_at',
        'updated_at',
        'deleted_at',
        'module',
    ];

    protected $casts = [
        'bookingid' => 'string',
        'item_id' => 'string',
        'hostid' => 'string',
        'guest_rating' => 'string',
        'host_rating' => 'string',
        'guestid' => 'string',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function guest()
    {
        return $this->belongsTo(AppUser::class, 'guestid');
    }

    public function host()
    {
        return $this->belongsTo(AppUser::class, 'hostid');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'bookingid');
    }
}
