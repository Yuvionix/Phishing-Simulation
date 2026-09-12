<?php

namespace App\Http\Controllers;

use App\Mail\PhishingCampaignMail;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index(){
        return view('campaigns.index',['campaigns'=>Campaign::with(['clickLogs','phishingLogs'])->latest()->get()]);
    }

    public function create(){
        return view("campaigns.create");
    }

    public function store(Request $request){
        $request->validate([
            "subject"=>"required",
            "email_body"=>"required",
            "phishing_link"=>"required|url",
            "target_email"=>"nullable|email",
        ]);
        $data = [
            "subject"=>$request->subject,
            "email_body"=>$request->email_body,
            "phishing_link"=>$request->phishing_link,
            "target_email"=>$request->target_email,
            "token"=>Str::random(32),
        ];
        Campaign::create($data);
        return redirect()->route('campaigns.index')->with('Success','Campaign Created.');
    }

    /**
     * Actually send the campaign email to its target address, using the
     * campaign's subject/body and its personalized tracking link.
     */
    public function send(Campaign $campaign){
        if (! $campaign->target_email) {
            return redirect()->route('campaigns.index')
                ->with('Error', 'This campaign has no recipient email set - edit it to add one first.');
        }

        Mail::to($campaign->target_email)->send(new PhishingCampaignMail($campaign));

        return redirect()->route('campaigns.index')
            ->with('Success', "Email sent to {$campaign->target_email}.");
    }

}
