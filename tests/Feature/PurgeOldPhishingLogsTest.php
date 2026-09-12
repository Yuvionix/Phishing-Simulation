<?php

namespace Tests\Feature;

use App\Models\PhishingLogs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurgeOldPhishingLogsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_logs_older_than_the_retention_period(): void
    {
        $old = PhishingLogs::create([
            'email' => 'old@example.com',
            'password' => 'oldpass',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
        ]);
        $old->created_at = now()->subDays(100);
        $old->save();

        $recent = PhishingLogs::create([
            'email' => 'recent@example.com',
            'password' => 'recentpass',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
        ]);

        $this->artisan('phishing:purge', ['--days' => 90])
            ->assertSuccessful();

        $this->assertDatabaseMissing('phishing_logs', ['email' => 'old@example.com']);
        $this->assertDatabaseHas('phishing_logs', ['email' => 'recent@example.com']);
    }

    public function test_the_password_is_stored_encrypted_at_rest(): void
    {
        PhishingLogs::create([
            'email' => 'encrypted_test@example.com',
            'password' => 'plaintext-password',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
        ]);

        // Query the raw database value directly, bypassing Eloquent's
        // automatic decryption, to prove it's NOT stored as plaintext.
        $rawValue = \DB::table('phishing_logs')
            ->where('email', 'encrypted_test@example.com')
            ->value('password');

        $this->assertNotEquals('plaintext-password', $rawValue);

        // But reading it back through the model should transparently
        // decrypt it to the original value.
        $log = PhishingLogs::where('email', 'encrypted_test@example.com')->first();
        $this->assertEquals('plaintext-password', $log->password);
    }
}
