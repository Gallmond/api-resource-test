<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    use HasFactory;
    
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function analytics(): HasOne
    {
        return $this->hasOne(Post::class, 'id', 'id');
    }

}
