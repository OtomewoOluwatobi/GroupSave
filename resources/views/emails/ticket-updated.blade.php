@extends('emails.layouts.email')

@section('subject', $title . ' — ' . $ticketId)
@section('eyebrow', 'Support')
@section('icon', '🔔')
@section('icon_style', 'ic-violet')
@section('title', $title)
@section('subtitle', 'Your support ticket status has changed')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>!</p>
  <p>{{ $message }}</p>

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
      <span class="ilb">Status</span>
      <span class="ivl @if($status === 'resolved') green @elseif($status === 'closed') @else gold @endif">{{ ucfirst($status) }}</span>
    </div>
  </div>

  @if($status === 'resolved')
  <div class="alert a-success">
    ✅ <strong>Issue Resolved!</strong> If you're satisfied with the resolution, no further action is needed. The ticket will be automatically closed.
  </div>
  @elseif($status === 'closed')
  <div class="alert a-success">
    📁 <strong>Ticket Closed.</strong> Thank you for contacting support. If you need further assistance, feel free to open a new ticket.
  </div>
  @endif

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/support/tickets/{{ $ticketId }}" class="btn-g">View Ticket</a>
  </div>
@endsection
