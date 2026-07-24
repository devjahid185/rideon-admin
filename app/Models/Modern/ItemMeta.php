<?php

namespace App\Models\Modern;

use Illuminate\Database\Eloquent\Model;

class ItemMeta extends Model
{
    protected $table = 'rental_item_meta';

    protected $fillable = [
        'item_id',
        'meta_key',
        'meta_value',
        'created_at',
        'updated_at',
    ];

    public static function getMetaValue($rentalItemId, $metaKey)
    {
        return self::where('item_id', $rentalItemId)
            ->where('meta_key', $metaKey)
            ->value('meta_value');
    }

    // Define your relationships or other methods here
}
