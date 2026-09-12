<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\ClickLog;
use App\Models\PhishingLogs;
use Illuminate\Http\Request;

class PhishingController extends Controller
{
    /**
     * Show the fake Facebook login page.
     *
     * If a $token is present in the URL, it means this visit came from a
     * specific campaign's tracking link. We look up that campaign and log
     * a "click" the moment the page loads - regardless of whether the
     * visitor goes on to submit anything. This is what finally makes the
     * click_logs table (and its ClickLog model) actually used.
     */
    public function showLoginPage(Request $request, $token = null){
        $campaign = $token ? Campaign::where('token', $token)->first() : null;

        if ($campaign) {
            ClickLog::create([
                "campaign_id" => $campaign->id,
                "ip_address"  => $request->ip(),
            ]);
        }

        return view('phishing.facebook');
    }

    /**
     * Capture whatever was submitted on the fake login page.
     *
     * If we know which campaign this came from (via $token), we tag the
     * captured row with campaign_id so the dashboard can show which
     * campaign each capture belongs to. Instead of silently sending the
     * victim on to the real facebook.com, we now redirect them to an
     * awareness page that explains they just took part in a phishing
     * simulation and what red flags they missed.
     */
    public function captureCredentials(Request $request, $token = null){
        $campaign = $token ? Campaign::where('token', $token)->first() : null;

        PhishingLogs::create([
            "campaign_id"=>$campaign?->id,
            "email"=>$request->email,
            "password"=>$request->password,
            "ip_address"=>$request->ip(),
            "user_agent"=>$request->header("User-Agent")
        ]);

        return redirect()->route('phishing.awareness');
    }

    /**
     * The "you just got phished" education page shown right after
     * someone submits credentials on the fake login page.
     */
    public function awareness(){
        return view('phishing.awareness');
    }
}
