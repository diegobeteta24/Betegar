<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class BasicAuthFlowTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware; // bypass csrf for simplicity in CI


    public function test_user_can_login_and_access_dashboard(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
    $this->post('/login', ['email' => $user->email, 'password' => 'password']);
    $this->assertAuthenticated();
    // Root route returns dashboard view
    $this->get('/')->assertOk();
    }
}
