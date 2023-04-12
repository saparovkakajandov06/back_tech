<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUserCanUpdateDetails()
    {
        $user = User::factory()->create();
        $newName = Str::random(8);

        $response = $this->post('/api/user', [
            'name' => $newName,
            'api_token' => $user->api_token,
        ])->assertStatus(200);

        $user->refresh();
        $this->assertEquals($newName, $user->name);
    }

    public function testSearchForParentShouldSetNull()
    {
        $user = User::factory()->create();
        $this->assertNull($user->parent_id);
        $user->searchForParent('bad_code');
        $this->assertNull($user->parent_id);
    }
}
