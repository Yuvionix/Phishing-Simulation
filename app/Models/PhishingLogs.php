<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhishingLogs extends Model
{
    protected $fillable = ["campaign_id","email","password","ip_address","user_agent"];

    /**
     * The campaign this captured credential came from (if the victim used
     * a personalized tracking link rather than the generic test URL).
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
