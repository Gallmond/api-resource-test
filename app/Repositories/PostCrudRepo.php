<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\PostAnalytics;
use App\Models\User;

class PostCrudRepo extends AutoCrudRepo
{
  protected string $model = Post::class;
  protected array $modelRelations = [
    'author' => User::class,
    'analytics' => PostAnalytics::class
  ];
}