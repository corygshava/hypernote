<?php

namespace App\Models;

use App\Traits\TimeRangeScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Posts extends Model {
    use TimeRangeScope;

    protected $fillable = [
        'user_id',
        'privacy_state',
        'title',
        'body',
        'metadata',
        'likes_count',
        'dislikes_count',
        'comments_count',
        'is_shadowed',
        'tags',
        'doctype',
    ];

    protected $casts = [
        'metadata' => 'array',
        'tags' => 'array',
        'is_shadowed' => 'boolean',
    ];

    public $state_map = [
        '0' => 'draft',
        '1' => 'public',
        '2' => 'private',
        '3' => 'unlisted',
        '4' => 'temporary',
        '5' => 'shadowed',
        '6' => 'anonymous',
    ];

    public $valid_doctypes = ['text','markdown','code'];

    public function myUser() : BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
}
