<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Laravel\Passport\Passport;
use Tests\TestCase;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertSame;

/**
 * @group rest
 */
class ApiResourceTest extends TestCase
{
    use DatabaseMigrations;

    public function testPostApiResourceController(): void
    {
        // test user
        User::factory()->create([
            'id' => 111
        ]);
        Passport::actingAs(User::find(111));

        // ----- CREATE
        $request = [
            'data' => [
                'title' => 'cool title',
                'content' => 'cool content',
                'analytics' => [
                    'analyticsViews' => 1,
                    'analyticsFavourites' => 2,
                    'analyticsDislikes' => 3,
                ]
            ],
            'with' => [
                'analytics'
            ]

        ];
        $response = $this->json('POST', route('posts.store'), $request);
        $response->assertStatus(201);
        $this->assertArrayHasKey('data', $response->json());

        // check main post info
        $post = $response->json('data');
        $postId = $response->json('data.id');
        $this->assertSame($request['data']['title'], $post['title']);
        $this->assertSame($request['data']['content'], $post['content']);

        // check the analytics info
        $analytics = $response->json('data.analytics');
        $reqAttr = $request['data']['analytics'];

        $this->assertSame($reqAttr['analyticsViews'], $analytics['analyticsViews']);
        $this->assertSame($reqAttr['analyticsFavourites'], $analytics['analyticsFavourites']);
        $this->assertSame($reqAttr['analyticsDislikes'], $analytics['analyticsDislikes']);
        
        
        // ----- READ

        // get without relation
        $response = $this->get(route('posts.show', ['post' => $postId]));
        $response->assertStatus(200);
        $post = $response->json('data');
        $this->assertSame($request['data']['title'], $post['title']);
        $this->assertSame($request['data']['content'], $post['content']);
        $this->assertArrayNotHasKey('analytics', $post);

        // get with relation
        $queryString = Arr::query([ 'with' => ['analytics'] ]);
        $uri = route('posts.show', ['post' => $postId]) . "?$queryString";
        $response = $this->get($uri);
        $response->assertStatus(200);
        $this->assertArrayHasKey('analytics', $response->json('data'));
        $analytics = $response->json('data.analytics');

        // ----- UPDATE
        
        // change the content of the post
        $url = route('posts.update', ['post' => $postId]);
        $data = [ 'data' => [ 'content' => 'New content!!!' ] ];
        $response = $this->json('PATCH', $url, $data);
        $response->assertStatus(200);
        $this->assertSame($data['data']['content'], $response->json('data.content'));

        // change the content of analytics through the post update
        $url = route('posts.update', ['post' => $postId]);
        $data = [
            'data' => [ 'analytics' => [ 'analyticsDislikes' => 999 ] ],
            'with' => [ 'analytics' ],
        ];
        $response = $this->json('PATCH', $url, $data);
        $response->assertStatus(200);
        $this->assertSame($data['data']['analytics']['analyticsDislikes'], $response->json('data.analytics.analyticsDislikes'));

        //DELETE
        $uri = route('posts.destroy', ['post' => $postId]);
        $response = $this->delete($uri);
        $response->assertStatus(200);
        $this->assertSame(true, $response->json('data.success'));
        $this->assertDatabaseCount(Post::class, 0);

    }

}
