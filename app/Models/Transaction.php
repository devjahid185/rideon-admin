<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_id',
        'transaction_id',
        'amount',
        'payment_status',
        'gateway_name',
        'response_data',
    ];

    protected $casts = [
        'booking_id' => 'string',
        'amount' => 'string',

    ];
}
