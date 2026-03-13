@extends('emails.layouts.email')

@section('subject', 'Password Reset Request')
@section('eyebrow', 'Account Security')
@section('icon', '🔐')
@section('icon_style', 'ic-dark')
@section('title', 'Password Reset Request')
@section('subtitle', 'Use the code below to reset your password')

@section('content')
  <p>Hello <strong>{{ $name }}</strong>,</p>
  <p>We received a request to reset the password for your account linked to <strong>{{ $email }}</strong>. Use the code below:</p>

  <div class="cblock">
    <div class="clabel">Your Reset Code</div>
    <div class="cval">{{ $resetCode }}</div>
    <div class="cexp">Expires in 15 minutes — do not share this code with anyone</div>
  </div>

  <div class="icard">
    <div class="ilabel">How to reset your password</div>
    @foreach(['Go to the password reset page', 'Enter your email: ' . $email, 'Enter the reset code above', 'Create and confirm your new password'] as $i => $step)
    <div class="irow">
      <span class="ilb" style="display:flex;align-items:center;gap:10px;">
        <span style="width:22px;height:22px;border-radius:50%;background:rgba(200,164,90,0.12);border:1.5px solid rgba(200,164,90,0.3);color:#a07c35;font-size:11px;font-weight:800;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $i + 1 }}</span>
        {{ $step }}
      </span>
    </div>
    @endforeach
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/reset-password?email={{ urlencode($email) }}" class="btn-d">Reset Password Now</a>
  </div>

  <div class="alert a-danger">
    🔒 <strong>Didn't request this?</strong> Ignore this email — your account remains secure. If you're concerned, contact support immediately.
  </div>
@endsection
