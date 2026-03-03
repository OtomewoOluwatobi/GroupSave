@extends('emails.layouts.email')

@section('subject', 'New Login Detected')
@section('eyebrow', 'Account Security')
@section('icon', '🔓')
@section('icon_style', 'ic-dark')
@section('title', 'New Login Detected')
@section('subtitle', 'A new device accessed your account')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>,</p>
  <p>We detected a new login to your GroupSave account.</p>

  <div class="icard">
    <div class="ilabel">Login Details</div>
    <div class="irow">
      <span class="ilb">Time</span>
      <span class="ivl">{{ $loginTime }}</span>
    </div>
    <div class="irow">
      <span class="ilb">IP Address</span>
      <span class="ivl">{{ $ipAddress }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Device</span>
      <span class="ivl">{{ $device }}</span>
    </div>
  </div>

  <div class="alert a-success">
    ✅ <strong>Was this you?</strong> If you recognize this login, no action is needed.
  </div>

  <div class="alert a-danger">
    🚨 <strong>Wasn't you?</strong> If you did not log in, please change your password immediately and contact our support team.
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/auth/change-password" class="btn-d">Secure My Account</a>
  </div>
@endsection
