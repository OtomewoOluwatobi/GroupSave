@extends('emails.layouts.email')

@section('subject', 'New Reply on Ticket ' . $ticketId)
@section('eyebrow', 'Support')
@section('icon', '💬')
@section('icon_style', 'ic-gold')
@section('title', 'New Reply')
@section('subtitle', 'Your support ticket has been updated')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>!</p>
  <p><strong>{{ $author }}</strong> replied to your support ticket.</p>

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
      <span class="ilb">From</span>
      <span class="ivl">{{ $author }}</span>
    </div>
  </div>

  <div class="icard">
    <div class="ilabel">Message Preview</div>
    <p style="margin:0;font-size:14px;color:#3d3550;line-height:1.65;">{{ $messagePreview }}</p>
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/support/tickets/{{ $ticketId }}" class="btn-g">View Full Conversation</a>
  </div>
@endsection
