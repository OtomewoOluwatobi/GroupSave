@extends('emails.layouts.email')

@section('subject', 'Verify Your Email Address')
@section('eyebrow', 'Email Verification')
@section('icon', '✉️')
@section('icon_style', 'ic-violet')
@section('title', 'Verify Your Email Address')
@section('subtitle', 'One quick step to unlock your account')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>,</p>
  <p>Please verify your email address to complete your <strong>{{ config('app.name') }}</strong> account setup and gain full access.</p>

  <div class="btn-wrap">
    <a href="{{ $verificationUrl }}" class="btn-g">Verify Email Address</a>
  </div>

  <p style="font-size:13px;color:#6b6578;">Or copy this link into your browser:</p>
  <div class="url">{{ $verificationUrl }}</div>

  <div class="alert a-warn">
    ⏰ <strong>Time sensitive:</strong> This link expires in <strong>{{ $expiresIn }} minutes</strong>.
  </div>

  <p>If you didn't create this account, please <a href="mailto:support@{{ strtolower(config('app.name')) }}.com" style="color:#a07c35;">contact our support team</a> immediately.</p>
@endsection
