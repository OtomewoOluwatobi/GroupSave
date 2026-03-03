@extends('emails.layouts.email')

@section('subject', 'You Earned Referral Points!')
@section('eyebrow', 'Rewards')
@section('icon', '🎁')
@section('icon_style', 'ic-gold')
@section('title', 'Points Earned!')
@section('subtitle', 'Your referral just verified their account')

@section('content')
  <p>Congratulations <strong>{{ $userName }}</strong>!</p>
  <p>You've earned referral points because <strong>{{ $referredUserName }}</strong> verified their account.</p>

  <div class="ablock">
    <div class="alabel">Points Earned</div>
    <div class="aval"><span class="cur">+</span>{{ $points }}</div>
    <div class="adesc">Referral bonus from {{ $referredUserName }}</div>
  </div>

  <div class="icard">
    <div class="ilabel">Referral Summary</div>
    <div class="irow">
      <span class="ilb">Referred User</span>
      <span class="ivl">{{ $referredUserName }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Points Earned</span>
      <span class="ivl gold">+{{ $points }}</span>
    </div>
  </div>

  <div class="alert a-success">
    🌟 <strong>Keep it up!</strong> Share your referral code with more friends to earn additional rewards.
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/referrals" class="btn-g">View My Referrals</a>
  </div>
@endsection
