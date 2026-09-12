<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClickLog extends Model
{
    protected $fillable = ["campaign_id", "user_id", "ip_address"];

    /**
     * The campaign whose tracking link was clicked.
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
