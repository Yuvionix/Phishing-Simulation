<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhishingLogs extends Model
{
    protected $fillable = ["campaign_id","email","password","ip_address","user_agent"];

    /**
     * Encrypt the captured password at rest. Laravel transparently
     * encrypts it before saving and decrypts it when read back in PHP -
     * so $log->password still works normally everywhere in the app, but
     * the raw database file itself only ever stores ciphertext.
     */
    protected $casts = [
        'password' => 'encrypted',
    ];

    /**
     * The campaign this captured credential came from (if the victim used
     * a personalized tracking link rather than the generic test URL).
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
