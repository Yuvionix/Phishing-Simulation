<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\ClickLog;
use App\Models\PhishingLogs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhishingCaptureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Submitting the fake login form on the generic (token-less) URL
     * should create a phishing_logs row with no campaign attached.
     */
    public function test_submitting_credentials_creates_a_phishing_log(): void
    {
        $response = $this->post('/facebook-login', [
            'email' => 'victim@example.com',
            'password' => 'hunter2',
        ]);

        $this->assertDatabaseHas('phishing_logs', [
            'email' => 'victim@example.com',
            'campaign_id' => null,
        ]);

        // The password column is encrypted at rest (see PhishingLogs::$casts),
        // so we check the decrypted value through the model instead of the
        // raw database row.
        $log = PhishingLogs::where('email', 'victim@example.com')->first();
        $this->assertEquals('hunter2', $log->password);

        // After Bug-Fix improvements: the victim is redirected to the
        // awareness page, not straight to the real facebook.com.
        $response->assertRedirect(route('phishing.awareness'));
    }

    /**
     * Visiting a campaign's personalized tracking link should immediately
     * log a click - even before any credentials are submitted.
     */
    public function test_visiting_a_campaign_tracking_link_logs_a_click(): void
    {
        $campaign = Campaign::create([
            'subject' => 'Test Campaign',
            'email_body' => 'Body text',
            'phishing_link' => 'http://127.0.0.1:8000/facebook-login',
            'token' => 'test-token-123',
        ]);

        $this->get('/facebook-login/test-token-123');

        $this->assertDatabaseHas('click_logs', [
            'campaign_id' => $campaign->id,
        ]);
    }

    /**
     * Submitting credentials through a campaign's tracking link should tag
     * the captured row with that campaign's ID, so the dashboard can show
     * which campaign each capture came from.
     */
    public function test_submitting_via_a_campaign_token_links_the_capture_to_that_campaign(): void
    {
        $campaign = Campaign::create([
            'subject' => 'Linked Campaign',
            'email_body' => 'Body text',
            'phishing_link' => 'http://127.0.0.1:8000/facebook-login',
            'token' => 'link-token-456',
        ]);

        $this->post('/facebook-login/link-token-456', [
            'email' => 'linked_victim@example.com',
            'password' => 'letmein',
        ]);

        $log = PhishingLogs::where('email', 'linked_victim@example.com')->first();

        $this->assertNotNull($log);
        $this->assertEquals($campaign->id, $log->campaign_id);
    }
}
