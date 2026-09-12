<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ["subject","email_body","phishing_link","token","target_email"];

    /**
     * The credentials that were captured through this campaign's tracking link.
     */
    public function phishingLogs()
    {
        return $this->hasMany(PhishingLogs::class);
    }

    /**
     * Every time someone opened this campaign's tracking link (whether or not
     * they submitted credentials).
     */
    public function clickLogs()
    {
        return $this->hasMany(ClickLog::class);
    }

    /**
     * The full, shareable tracking link for this campaign.
     */
    public function trackingUrl(): string
    {
        return route('phishing.login', ['token' => $this->token]);
    }
}
