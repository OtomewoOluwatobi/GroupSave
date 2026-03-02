@extends('emails.layouts.email')

@section('subject', 'You Have Been Removed from ' . $groupName)
@section('eyebrow', 'Group Update')
@section('icon', '👤')
@section('icon_style', 'ic-red')
@section('title', 'Removed from Group')
@section('subtitle', 'Your group membership has changed')

@section('content')
  <p>Hello,</p>
  <p>You have been removed from the savings group <strong>"{{ $groupName }}"</strong>.</p>

  @if($reason)
  <div class="icard">
    <div class="ilabel">Reason</div>
    <p style="margin:0;font-size:14px;color:#3d3550;line-height:1.65;">{{ $reason }}</p>
  </div>
  @endif

  <p>If you believe this is an error or would like more information, please contact the group administrator or reach out to our support team.</p>

  <div class="btn-wrap">
    <a href="mailto:support@digitalAjoLedger.com" class="btn-o">Contact Support</a>
  </div>
@endsection
