@extends('emails.layouts.email')

@section('subject', 'Join Request Approved — ' . $groupTitle)
@section('eyebrow', 'Group Update')
@section('icon', '✅')
@section('icon_style', 'ic-green')
@section('title', 'Request Approved!')
@section('subtitle', 'You\'re now a member of the group')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>,</p>
  <p>Great news! Your request to join <strong>"{{ $groupTitle }}"</strong> has been approved.</p>

  <div class="icard">
    <div class="ilabel">Group Details</div>
    <div class="irow">
      <span class="ilb">Group</span>
      <span class="ivl">{{ $groupTitle }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Status</span>
      <span class="ivl green">Active Member</span>
    </div>
  </div>

  <div class="alert a-success">
    🎉 <strong>Welcome to the group!</strong> You can now participate in contributions and view the shared ledger.
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/groups/{{ $groupId }}" class="btn-g">View Group</a>
  </div>
@endsection
