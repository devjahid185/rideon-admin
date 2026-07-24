<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalItemRule extends Model
{
    use HasFactory;

    protected $table = 'rental_item_rules';

    protected $fillable = [
        'rule_name',
        'status',
        'module',
    ];

    public function moduleGet()
    {
        return $this->belongsTo(Module::class, 'module');
    }

    public static function getRuleNamesByIds($ids)
    {
        return RentalItemRule::whereIn('id', $ids)->pluck('rule_name')->toArray();
    }
}
