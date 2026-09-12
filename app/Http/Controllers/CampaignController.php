<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
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
        ]);
        $data = [
            "subject"=>$request->subject,
            "email_body"=>$request->email_body,
            "phishing_link"=>$request->phishing_link,
            "token"=>Str::random(32),
        ];
        Campaign::create($data);
        return redirect()->route('campaigns.index')->with('Success','Campaign Created.');
    }

}
