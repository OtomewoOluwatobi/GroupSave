@extends('emails.layouts.email')

@section('subject', 'Your Password Has Been ' . ucfirst($action))
@section('eyebrow', 'Account Security')
@section('icon', '🔒')
@section('icon_style', 'ic-dark')
@section('title', 'Password ' . ucfirst($action))
@section('subtitle', 'Your account credentials have changed')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>,</p>
  <p>Your Digital Ajo Ledger password was successfully <strong>{{ $action }}</strong> on <strong>{{ $changedAt }}</strong>.</p>

  <div class="alert a-success">
    ✅ <strong>Everything looks good.</strong> If you made this change, no further action is needed.
  </div>

  <div class="alert a-danger">
    🚨 <strong>Didn't do this?</strong> Your account may be at risk. Reset your password immediately and contact our support team.
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/support" class="btn-d">Contact Support</a>
  </div>
@endsection
