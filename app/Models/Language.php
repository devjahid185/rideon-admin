<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    public $table = 'languages';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'short_name',
        'language_status',
        'created_at',
        'updated_at',
    ];
}
