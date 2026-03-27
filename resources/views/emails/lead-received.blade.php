@extends('emails.layouts.email')

@section('subject', 'We received your response — ' . config('app.name'))
@section('eyebrow', 'Thank You')
@section('icon', '✅')
@section('icon_style', 'ic-gold')
@section('title', 'Response Received!')
@section('subtitle', 'We\'ll be in touch soon')

@section('content')
  <p>Hello <strong>{{ $name }}</strong>!</p>
  <p>Thanks for taking the time to share your thoughts with us. We've recorded your responses and our team will review them shortly.</p>

  <div class="alert a-success">
    🎉 <strong>You're on the list!</strong> We'll reach out as soon as we have something exciting to share.
  </div>

  <p style="font-size:13px;color:#6b6578;">If you have any questions in the meantime, feel free to reply to this email.</p>
@endsection
