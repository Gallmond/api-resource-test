<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostAnalytics;
use App\Models\User;

class PostCrudRepo extends AutoCrudRepo
{
  protected string $model = Post::class;

  /**
   * 0: Model class
   * 1: column on this repo's model that points at the related model
   * 2: related model's PK column
   */
  protected array $modelRelations = [
    'author' => [User::class, 'user_id'],
    'analytics' => [PostAnalytics::class, 'id'],
    'comments' => [Comment::class, 'post_id'],
  ];

}