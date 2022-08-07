<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group test
 */
class DebugTest extends TestCase
{

    public function testDebugWorkAsExpected(): void
    {

        $request = [ 'data' => [ 'fizz' => 'buzz' ] ];

        $response = $this->json('POST', route('test.test'), $request);
        $response->assertStatus(200);
    }

}
