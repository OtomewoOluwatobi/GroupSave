@extends('emails.layouts.email')

@section('subject', "You've Been Invited to Join {{ $groupName }}")
@section('eyebrow', 'Group Invitation')
@section('icon', '👥')
@section('icon_style', 'ic-violet')
@section('title', "You've Been Invited!")
@section('subtitle', 'Someone wants you in their savings group')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>,</p>
  <p><strong>{{ $inviterName }}</strong> has invited you to join the savings group <strong>"{{ $groupName }}"</strong> on GroupSave.</p>

  @isset($groupDetails)
  <div class="icard">
    <div class="ilabel">Group Details</div>
    @foreach($groupDetails as $label => $value)
    <div class="irow">
      <span class="ilb">{{ $label }}</span>
      <span class="ivl">{{ $value }}</span>
    </div>
    @endforeach
  </div>
  @endisset

  @isset($generatedPassword)
  <div class="alert a-success">
    ✅ <strong>A new account has been created for you.</strong> Use the credentials below to sign in for the first time, then change your password.
  </div>
  <table class="ctable">
    <thead>
      <tr>
        <th>Email Address</th>
        <th>Temporary Password</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $userEmail ?? '' }}</td>
        <td>{{ $generatedPassword }}</td>
      </tr>
    </tbody>
  </table>
  @endisset

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/groups/{{ $groupId ?? '' }}" class="btn-g">Accept Invitation</a>
  </div>

  <p style="font-size:13px;color:#6b6578;">If you did not expect this invitation, you can safely ignore this email.</p>
@endsection
