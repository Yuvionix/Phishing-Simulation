<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\FakesViteManifest;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;
    use FakesViteManifest;

    protected function tearDown(): void
    {
        $this->removeFakeViteManifest();
        parent::tearDown();
    }

    /**
     * A guest (not logged in) visiting /dashboard should be redirected to
     * the login page, never shown any captured data.
     */
    public function test_guests_are_redirected_away_from_the_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * A guest visiting /campaigns should also be redirected to login.
     */
    public function test_guests_are_redirected_away_from_campaigns(): void
    {
        $response = $this->get('/campaigns');

        $response->assertRedirect('/login');
    }

    /**
     * A logged-in user should be able to view the dashboard successfully.
     */
    public function test_authenticated_users_can_view_the_dashboard(): void
    {
        $this->fakeViteManifest();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Phishing Logs');
    }
}
