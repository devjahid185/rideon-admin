<?php

// Wallet.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'type', 'description', 'status', 'currency',
    ];

    protected $casts = [
        'amount' => 'string',
        'user_id' => 'string',
        'status' => 'string',

    ];
    // Define any additional items or methods you need

    public function appUser()
    {
        return $this->belongsTo(AppUser::class, 'user_id');
    }
}
