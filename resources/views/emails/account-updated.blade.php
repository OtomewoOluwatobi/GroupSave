@extends('emails.layouts.email')

@section('subject', 'Your Account Information Was Updated')
@section('eyebrow', 'Account Security')
@section('icon', '🛡️')
@section('icon_style', 'ic-dark')
@section('title', 'Account Information Updated')
@section('subtitle', 'Changes were made to your account')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>,</p>
  <p>Your account information was updated on <strong>{{ $updatedAt }}</strong>. Here's a summary of what changed:</p>

  <div class="icard">
    <div class="ilabel">Updated Fields</div>
    @foreach($updatedFields as $field)
    <div class="irow">
      <span class="ilb">{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
      <span class="ivl gold">Updated</span>
    </div>
    @endforeach
  </div>

  <div class="alert a-warn">
    ⚠️ <strong>Wasn't you?</strong> If you did not make these changes, please secure your account immediately by resetting your password and contacting support.
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/profile" class="btn-d">Review My Account</a>
  </div>
@endsection
