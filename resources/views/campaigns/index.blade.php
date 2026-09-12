@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Phishing Campaigns</h2>
    <a href="{{route('campaigns.create')}}" class="btn btn-primary mb-3">Create Campaign</a>
    <table class="table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Reference Link</th>
                <th>Trackable Link (share this one)</th>
                <th>Clicks</th>
                <th>Credentials Captured</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($campaigns as $campaign)
                <tr>
                    <td>{{$campaign->subject}}</td>
                    <td><a href="{{$campaign->phishing_link}}" target="_blank">{{$campaign->phishing_link}}</a></td>
                    <td>
                        <code class="small">{{$campaign->trackingUrl()}}</code>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-1"
                                onclick="navigator.clipboard.writeText('{{$campaign->trackingUrl()}}')">
                            Copy
                        </button>
                    </td>
                    <td>{{$campaign->clickLogs->count()}}</td>
                    <td>{{$campaign->phishingLogs->count()}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
