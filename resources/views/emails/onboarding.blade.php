@extends('emails.layouts.email')

@section('subject', 'Welcome to {{ config("app.name") }}!')
@section('eyebrow', 'Welcome Aboard')
@section('icon', '🎉')
@section('icon_style', 'ic-gold')
@section('title', 'Welcome to {{ config("app.name") }}!')
<blade
    section|(%26%2339%3Bsubtitle%26%2339%3B%2C%20%26%2339%3BYour%20community%20savings%20journey%20starts%20here%26%2339%3B) />

@section('content')
<p>Hello <strong>{{ $userName }}</strong>,</p>
<p>Thank you for joining <strong>{{ config('app.name') }}</strong>. We're thrilled to have you in our community of disciplined,
    transparent savers.</p>

<div class="icard">
    <div class="ilabel">Here's what you can do</div>
    <ul class="flist" style="margin:0;">
        <li><span class="chk">✓</span> Create or join savings groups with friends &amp; family</li>
        <li><span class="chk">✓</span> Set monthly contribution targets and payout dates</li>
        <li><span class="chk">✓</span> Track every contribution on a shared transparent ledger</li>
        <li><span class="chk">✓</span> Earn reward points for referrals and cycle completions</li>
    </ul>
</div>

<p>Before you can start saving, please verify your email address:</p>

<div class="btn-wrap">
    <a href="{{ $verificationUrl }}" class="btn-g">Verify My Email</a>
</div>

<p style="font-size:13px;color:#6b6578;">Or copy this link into your browser:</p>
<div class="url">{{ $verificationUrl }}</div>

<div class="alert a-warn">
    ⏰ <strong>Time sensitive:</strong> This link expires in <strong>{{ $expiresIn }} minutes</strong>.
</div>

<p>If you didn't create this account, you can safely ignore this email.</p>
@endsection
