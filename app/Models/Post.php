<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'body',
        'user_id'
    ];

    public function myuser() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
 