<?php

namespace App\Models;

use App\Models\User;
use App\Traits\TimeRangeScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class userRoles extends Model{
	use TimeRangeScope;

    public $fillable = [
        'role_name',
        'role_permissions',
    ];

    protected $casts = [
        'role_permissions' => 'array',
    ];

    public function users(): HasMany{
        return $this->hasMany(User::class);
    }
}
