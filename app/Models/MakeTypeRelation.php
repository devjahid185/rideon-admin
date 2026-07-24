<?php

namespace App\Models;

use App\Models\Modern\ItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakeTypeRelation extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'make_type_relation';

    // Define the primary key for the table
    protected $primaryKey = 'id';

    // If your primary key is not an auto-incrementing integer, set this to false
    public $incrementing = true;

    // Define the data type of the primary key
    protected $keyType = 'int';

    // Disable timestamps if your table does not have created_at and updated_at columns
    public $timestamps = false;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'make_id',
        'type_id',
    ];

    // Define the relationships
    public function vehicleMake()
    {
        return $this->belongsTo(VehicleMake::class, 'make_id');
    }

    public function ItemType()
    {
        return $this->belongsTo(ItemType::class, 'type_id');
    }
}
