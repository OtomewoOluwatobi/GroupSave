@extends('emails.layouts.email')

@section('subject', 'Support Ticket Created — ' . $ticketId)
@section('eyebrow', 'Support')
@section('icon', '📝')
@section('icon_style', 'ic-violet')
@section('title', 'Ticket Created')
@section('subtitle', 'We\'ve received your support request')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>!</p>
  <p>Your support ticket has been submitted successfully. Our team will review it and respond as soon as possible.</p>

  <div class="icard">
    <div class="ilabel">Ticket Details</div>
    <div class="irow">
      <span class="ilb">Ticket ID</span>
      <span class="ivl gold">{{ $ticketId }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Subject</span>
      <span class="ivl">{{ $subject }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Category</span>
      <span class="ivl">{{ ucfirst($category) }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Priority</span>
      <span class="ivl">{{ ucfirst($priority) }}</span>
    </div>
  </div>

  <div class="alert a-success">
    ⏱️ <strong>Expected Response Time:</strong> We'll respond within <strong>{{ $sla }}</strong>.
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/support/tickets/{{ $ticketId }}" class="btn-g">View Ticket</a>
  </div>

  <p style="font-size:13px;color:#6b6578;">You'll receive email notifications when we respond to your ticket.</p>
@endsection
