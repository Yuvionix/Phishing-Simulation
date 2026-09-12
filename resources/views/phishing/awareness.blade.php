@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-warning">
                <div class="card-header bg-warning">
                    <strong>⚠️ This was a phishing simulation</strong>
                </div>
                <div class="card-body">
                    <p class="lead">
                        You just submitted your credentials to a fake login page as part of an
                        authorized security-awareness test — not the real Facebook.
                    </p>

                    <p>No real account was accessed. This test exists to help you (and your
                    organization) recognize the warning signs before they show up in a real
                    attack. Here's what to look for next time:</p>

                    <ul>
                        <li><strong>Check the address bar.</strong> The real Facebook is always
                        at <code>facebook.com</code>. Anything else — a different domain, an IP
                        address, or a misspelled look-alike — is a red flag, no matter how
                        convincing the page looks.</li>

                        <li><strong>Be suspicious of urgency.</strong> Messages like "your account
                        will be locked" or "verify immediately" are designed to make you act
                        before you think.</li>

                        <li><strong>Hover before you click.</strong> On desktop, hovering over a
                        link (without clicking) shows you the real destination URL at the bottom
                        of the browser.</li>

                        <li><strong>Unexpected login prompts are suspicious.</strong> If you
                        weren't already trying to log in somewhere, a sudden login page is a
                        warning sign — especially from an email or text link.</li>

                        <li><strong>When in doubt, go direct.</strong> Instead of clicking a link
                        in a message, open the site yourself by typing the address you know, or
                        using a bookmark.</li>
                    </ul>

                    <p class="text-muted">If you have questions about this test, contact your
                    security team.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
