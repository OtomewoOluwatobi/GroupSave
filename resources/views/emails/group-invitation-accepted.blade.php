@extends('emails.layouts.email')

@section('subject', "{{ $userName }} Accepted Your Group Invitation")
@section('eyebrow', 'Group Update')
@section('icon', '🤝')
@section('icon_style', 'ic-green')
@section('title', 'Invitation Accepted!')
@section('subtitle', 'A new member has joined your group')

@section('content')
  <p>Hello <strong>{{ $adminName }}</strong>,</p>
  <p>Great news — <strong>{{ $userName }}</strong> has accepted your invitation to join <strong>"{{ $groupName }}"</strong>.</p>

  <div class="icard">
    <div class="ilabel">New Member Details</div>
    <div class="irow">
      <span class="ilb">Name</span>
      <span class="ivl">{{ $userName }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Email</span>
      <span class="ivl">{{ $userEmail }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Group</span>
      <span class="ivl">{{ $groupName }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Status</span>
      <span class="ivl green">Active Member</span>
    </div>
  </div>

  <div class="alert a-success">
    🎉 Your group is growing! Head to your dashboard to see the updated member list.
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/groups/{{ $groupId ?? '' }}" class="btn-g">View Group</a>
  </div>
@endsection
