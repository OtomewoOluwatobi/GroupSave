@extends('emails.layouts.email')

<blade
    section|(%26%2339%3Bsubject%26%2339%3B%2C%20%26%2339%3BYour%20%26%2339%3B%20.%20%24planName%20.%20%26%2339%3B%20Plan%20Is%20Now%20Active%26%2339%3B) />
@section('eyebrow', 'Subscription')
@section('icon', '🎉')
@section('icon_style', 'ic-green')
@section('title', 'Plan Activated!')
<blade
    section|(%26%2339%3Bsubtitle%26%2339%3B%2C%20%26%2339%3BYour%20subscription%20is%20live%20and%20ready%20to%20use%26%2339%3B) />

@section('content')
<p>Hello <strong>{{ $userName }}</strong>,</p>
<p>You've successfully subscribed to the <strong>{{ $planName }}</strong> plan. You can now access all the features
    included with your plan.</p>

<div class="icard">
    <div class="ilabel">Subscription Details</div>
    <div class="irow">
        <span class="ilb">Plan</span>
        <span class="ivl">{{ $planName }}</span>
    </div>
    <div class="irow">
        <span class="ilb">Status</span>
        <span class="ivl green">Active</span>
    </div>
    <div class="irow">
        <span class="ilb">Activated</span>
        <span class="ivl">{{ $activatedAt }}</span>
    </div>
</div>

<div class="alert a-success">
    ✅ <strong>You're all set!</strong> Start creating and managing your savings groups right away.
</div>

<div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/dashboard" class="btn-g">Go to Dashboard</a>
</div>
@endsection
