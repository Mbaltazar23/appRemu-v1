<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmpLiquidation extends Model {

    // The table associated with the model
    protected $table = 'tmp_liquidation';
    // We don't need to define the 'id' field as Laravel automatically handles it as an auto-incrementing primary key
    protected $primaryKey = 'id'; // Using 'id' as the auto-incrementing primary key
    // Define the fields that can be mass-assigned
    protected $fillable = [
        'tuition_id',
        'title',
        'value',
        'in_liquidation'
    ];

}
