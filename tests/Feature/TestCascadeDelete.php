<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;


class TestCascadeDelete extends TestCase
{
    
    /**
     * @group temp
     */
    public function testDeletingAPostAlsoDeletesComments(): void
    {
        $postAuthor = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $postAuthor->id
        ]);
        Comment::factory(5)->create([
            'post_id' => $post->id
        ]);

        $post = Post::with('comments')->find($post->id);
        $this->assertInstanceOf(Post::class, $post);
        $this->assertContainsOnlyInstancesOf(Comment::class, $post->comments);
        $this->assertCount(5, $post->comments);

        // delete the post
        $deleted = $post->delete();
        $this->assertTrue($deleted);

        // check they're gone from the database
        $this->assertDatabaseCount(Post::class, 0);
        $this->assertDatabaseCount(Comment::class, 0);
    }


}
