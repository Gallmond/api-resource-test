<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostAnalytics;
use Database\Factories\PostAnalyticsFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PostAnalyticsModelTest extends TestCase
{
    use DatabaseMigrations;

    public function testPostAndPostAnalyticsModelDoNotGetEachOthersFields(): void
    {

        $postAnalytics = (new PostAnalyticsFactory)->create();
        
        assert($postAnalytics instanceof PostAnalytics);
        assert($postAnalytics->post instanceof Post);

        // assert that analytics does not have post stuff
        $array = $postAnalytics->toArray();
        self::assertArrayNotHasKey('title', $array);
        self::assertArrayNotHasKey('content', $array);
        self::assertArrayNotHasKey('user_id', $array);

        // check that the post does not get the analytics stuff
        $array = $postAnalytics->post->toArray();
        self::assertArrayNotHasKey('analytics_views', $array);
        self::assertArrayNotHasKey('analytics_favourites', $array);
        self::assertArrayNotHasKey('analytics_dislikes', $array);
    }


}
