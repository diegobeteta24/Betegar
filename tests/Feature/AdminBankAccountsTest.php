<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminBankAccountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles table exists
        if (! class_exists(Role::class)) {
            $this->markTestSkipped('spatie/laravel-permission not set up');
        }
        // Idempotent ensure
        if (! Role::where('name','admin')->exists()) {
            Role::create(['name' => 'admin']);
        }
    }

    public function test_guest_cannot_access(): void
    {
        $this->getJson('/api/bank-accounts')->assertStatus(401);
    }

    public function test_non_admin_forbidden(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/bank-accounts')->assertStatus(403);
    }

    public function test_admin_ok(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        Sanctum::actingAs($user);

        $this->getJson('/api/bank-accounts')->assertOk();
    }
}
