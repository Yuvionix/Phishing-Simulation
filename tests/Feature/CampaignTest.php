<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every campaign created through the store() endpoint should get a
     * unique, non-empty token - this is what makes each campaign's
     * tracking link distinct from every other campaign's.
     */
    public function test_creating_a_campaign_generates_a_unique_token(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/campaigns', [
            'subject' => 'Campaign One',
            'email_body' => 'Body one',
            'phishing_link' => 'http://127.0.0.1:8000/facebook-login',
        ]);

        $this->actingAs($user)->post('/campaigns', [
            'subject' => 'Campaign Two',
            'email_body' => 'Body two',
            'phishing_link' => 'http://127.0.0.1:8000/facebook-login',
        ]);

        $tokens = Campaign::pluck('token');

        $this->assertCount(2, $tokens);
        $this->assertNotNull($tokens[0]);
        $this->assertNotNull($tokens[1]);
        $this->assertNotEquals($tokens[0], $tokens[1]);
    }

    /**
     * A campaign's trackingUrl() helper should build a real URL pointing
     * at the /facebook-login/{token} route with that campaign's own token.
     */
    public function test_tracking_url_resolves_to_the_correct_route(): void
    {
        $campaign = Campaign::create([
            'subject' => 'URL Test Campaign',
            'email_body' => 'Body text',
            'phishing_link' => 'http://127.0.0.1:8000/facebook-login',
            'token' => 'abc123token',
        ]);

        $this->assertStringContainsString('/facebook-login/abc123token', $campaign->trackingUrl());
    }

    /**
     * A guest (not logged in) should never be able to create a campaign -
     * the /campaigns routes are protected by the 'auth' middleware.
     */
    public function test_guests_cannot_create_campaigns(): void
    {
        $response = $this->post('/campaigns', [
            'subject' => 'Should Not Save',
            'email_body' => 'Body',
            'phishing_link' => 'http://127.0.0.1:8000/facebook-login',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('campaigns', 0);
    }
}
