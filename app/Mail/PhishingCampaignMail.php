<?php

namespace App\Mail;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PhishingCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public Campaign $campaign;

    /**
     * Create a new message instance.
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Get the message envelope (subject line, from address, etc.).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
        );
    }

    /**
     * Get the message content definition (which view renders the body).
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.phishing_campaign',
            with: [
                'body' => $this->campaign->email_body,
                'trackingUrl' => $this->campaign->trackingUrl(),
            ],
        );
    }
}
