<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingCancellationReason extends Model
{
    public $table = 'booking_cancellation_reasons';

    protected $primaryKey = 'order_cancellation_id';

    protected $fillable = [
        'order_cancellation_id ',
        'reason',
        'user_type',
        'status',
        'created_at',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => 'string',

    ];

    public function appUser()
    {
        return $this->belongsTo(AppUser::class, 'user_type');
    }
}
