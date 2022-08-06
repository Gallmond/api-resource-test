<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

use function PHPUnit\Framework\assertArrayHasKey;

/**
 * @group temp
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

        //CREATE
        $request = [
            'data' => [
                'title' => 'cool title',
                'content' => 'cool content'
            ]
        ];
        $response = $this->json('POST', route('posts.store'), $request);
        $response->assertStatus(201);
        assertArrayHasKey('data', $response->json());


        //READ

        //UPDATE

        //DELETE

    }

}
