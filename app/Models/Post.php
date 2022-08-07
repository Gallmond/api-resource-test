<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    /**
     * These are the fields that can be filled in by the CRUD endpoints
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    /**
     * These are the fields that will not be returned when this model is 
     * serialised to JSON
     *
     * @var array
     */
    protected $hidden = [
        'analytics_views',
        'analytics_favourites',
        'analytics_dislikes',
    ];

    protected array $hasRelations = [
        'author' => User::class,
        'analytics' => PostAnalytics::class,
    ];

    /**
     * Who created this post
     *
     * @return HasOne
     */
    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * The analytics model for this post (actualy the same table row)
     *
     * @return HasOne
     */
    public function analytics(): HasOne
    {
        return $this->hasOne(PostAnalytics::class, 'id', 'id');
    }
}
