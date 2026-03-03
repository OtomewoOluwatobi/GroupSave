@extends('emails.layouts.email')

@section('subject', 'Someone Used Your Referral Code!')
@section('eyebrow', 'Referral')
@section('icon', '👋')
@section('icon_style', 'ic-violet')
@section('title', 'New Referral!')
@section('subtitle', 'Someone signed up using your code')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>!</p>
  <p><strong>{{ $referredUserName }}</strong> just signed up using your referral code.</p>

  <div class="icard">
    <div class="ilabel">Referral Details</div>
    <div class="irow">
      <span class="ilb">New User</span>
      <span class="ivl">{{ $referredUserName }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Status</span>
      <span class="ivl" style="color:#c8a45a;">Pending Verification</span>
    </div>
  </div>

  <div class="alert a-warn">
    ⏳ <strong>Almost there!</strong> Once they verify their account, you'll earn your referral points automatically.
  </div>

  <p>Keep sharing your referral code to earn more rewards!</p>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/referrals" class="btn-g">View My Referrals</a>
  </div>
@endsection
