<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class systemData extends Model{
    /**
     * this model stores system specific data like all permissions, cache retainer time etc.
     */
    protected $fillable = [
        'item_name',
        'item_data',
    ];

    protected $casts = [
        'item_data' => 'array'
    ];
}
