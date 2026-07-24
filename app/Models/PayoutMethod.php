<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutMethod extends Model
{
    use HasFactory;

    protected $table = 'payout_method';

    protected $fillable = [
        'name',
        'status',
        'module',
    ];

    // public $timestamps = true;

    const STATUS_SELECT = [
        1 => 'Active',
        0 => 'Inactive',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function getNameAttribute($value)
    {
        $name = trim($value);

        if (strcasecmp($name, 'upi') === 0) {
            return strtoupper($name);
        }

        return ucwords($value);
    }
}
