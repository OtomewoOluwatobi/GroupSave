@extends('emails.layouts.email')

@section('subject', 'Join Request Declined — ' . $groupTitle)
@section('eyebrow', 'Group Update')
@section('icon', '❌')
@section('icon_style', 'ic-red')
@section('title', 'Request Declined')
@section('subtitle', 'Your join request was not approved')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>,</p>
  <p>Unfortunately, your request to join <strong>"{{ $groupTitle }}"</strong> has been declined by the group administrator.</p>

  <div class="icard">
    <div class="ilabel">Request Details</div>
    <div class="irow">
      <span class="ilb">Group</span>
      <span class="ivl">{{ $groupTitle }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Status</span>
      <span class="ivl" style="color:#dc2626;">Declined</span>
    </div>
  </div>

  <p>This doesn't mean you can't save with us! You can:</p>
  <ul class="flist" style="margin:0;">
    <li><span class="chk">✓</span> Browse and join other public savings groups</li>
    <li><span class="chk">✓</span> Create your own savings group and invite friends</li>
    <li><span class="chk">✓</span> Contact the group admin for more information</li>
  </ul>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/groups" class="btn-d">Explore Groups</a>
  </div>
@endsection
