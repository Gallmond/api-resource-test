<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'post_id',
        'content',
        'likes',
        'dislikes',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'post_id' => 'integer',
        'content' => 'string',
        'likes' => 'integer',
        'dislikes' => 'integer',
    ];

    protected $hidden = [
        'user_id', 'post_id'
    ];

    public function post(): HasOne
    {
        return $this->hasOne(Post::class, 'id', 'post_id');
    }

    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
