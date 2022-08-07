<?php

namespace App\Models;

use Database\Factories\PostAnalyticsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property integer $views
 * @property integer $favourites
 * @property integer $dislikes
 * @Relations
 * @property-read Post $post
 */
class PostAnalytics extends Model
{
    use HasFactory;

    protected $factory = PostAnalyticsFactory::class;

    protected $table = 'posts';

    protected $fillable = [
        'analytics_views',
        'analytics_favourites',
        'analytics_dislikes',
    ];

    protected $casts = [
        'analytics_views' => 'integer',
        'analytics_favourites' => 'integer',
        'analytics_dislikes' => 'integer',
    ];

    // hide user post cols
    protected $hidden = [
        'title',
        'content',
        'user_id',
    ];

    public function post(): HasOne
    {
        return $this->hasOne(Post::class, 'id', 'id');
    }

    public function getViewsAttribute(): int
    {
        return $this->getOriginal('analytics_views');
    }

    public function getFavouritesAttribute(): int
    {
        return $this->getOriginal('analytics_favourites');
    }

    public function getDislikesAttribute(): int
    {
        return $this->getOriginal('analytics_dislikes');
    }

}
