<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppUserMeta extends Model
{
    protected $table = 'app_user_meta';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'meta_key',
        'meta_value',
    ];

    /**
     * Define a belongsTo relationship with AppUser model.
     */
    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id', 'id');
    }
}
