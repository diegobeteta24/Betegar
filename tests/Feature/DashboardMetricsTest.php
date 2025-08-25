<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_metrics_requires_authentication(): void
    {
        $this->getJson('/api/dashboard/metrics')->assertStatus(401);
    }

    public function test_metrics_returns_basic_shape_when_authenticated(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $res = $this->getJson('/api/dashboard/metrics')->assertOk()->json();

        $this->assertArrayHasKey('generated_at', $res);
        $this->assertArrayHasKey('kpis', $res);
        $this->assertArrayHasKey('sales_purchases', $res);
        $this->assertArrayHasKey('expenses_cat', $res);
        $this->assertArrayHasKey('accounts', $res);
        $this->assertArrayHasKey('receivables', $res);
    }
}
