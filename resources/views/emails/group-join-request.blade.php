@extends('emails.layouts.email')

@section('subject', 'New Join Request for ' . $groupTitle)
@section('eyebrow', 'Group Admin')
@section('icon', '📥')
@section('icon_style', 'ic-violet')
@section('title', 'New Join Request')
@section('subtitle', 'Someone wants to join your group')

@section('content')
  <p>Hello <strong>{{ $adminName }}</strong>,</p>
  <p><strong>{{ $requesterName }}</strong> has requested to join your savings group <strong>"{{ $groupTitle }}"</strong>.</p>

  <div class="icard">
    <div class="ilabel">Requester Details</div>
    <div class="irow">
      <span class="ilb">Name</span>
      <span class="ivl">{{ $requesterName }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Email</span>
      <span class="ivl">{{ $requesterEmail }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Group</span>
      <span class="ivl">{{ $groupTitle }}</span>
    </div>
  </div>

  <div class="alert a-warn">
    ⏰ <strong>Action Required:</strong> Please review this request and approve or decline the membership.
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/groups/{{ $groupId }}/members" class="btn-g">Review Request</a>
  </div>
@endsection
