<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * What happens when we try to create/update a model using non-fillable columns
 * 
 * @group aaa
 */
class ModelFillableTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test that we cannot fill a model with non-fillable data
     */
    public function testCannotFillNonFillables(): void
    {
        $user = (new UserFactory)->create();

        $attributes = [
            'title' => 'some title',
            'created_at' => Carbon::now(),
            'user_id' => $user->id,
            'analytics_views' => 6969,
        ];

        Post::create( $attributes );
        self::assertDatabaseMissing(Post::class, $attributes);
        
        unset($attributes['analytics_views']);
        self::assertDatabaseHas(Post::class, $attributes);
    }

    /**
     * check that we cannot load a non-existant relation
     * ie we get a RelationNotFoundException
     */
    public function testCannotLoadNonExistantRelations(): void
    {
        $user = (new UserFactory)->create();
        $attributes = [
            'title' => 'some title',
            'user_id' => $user->id,
        ];

        $post = Post::create( $attributes );

        $postWithUser = Post::with(['author'])->findOrFail( $post->id );
        $this->assertInstanceOf(User::class, $post->author);
        $this->assertSame($user->email, $postWithUser->author->email);

        $this->expectException(RelationNotFoundException::class);
        Post::with(['author', 'foo'])
            ->findOrFail( $post->id );
    }

}
