@extends('emails.layouts.email')

@section('subject', 'Contribution Received — ' . $groupName)
@section('eyebrow', 'Contribution')
@section('icon', '💰')
@section('icon_style', 'ic-gold')
@section('title', 'Contribution Received')
@section('subtitle', 'Your group ledger has been updated')

@section('content')
  <p>Hello,</p>
  <p><strong>{{ $contributorName }}</strong> has made a contribution to <strong>"{{ $groupName }}"</strong>. The shared ledger has been updated.</p>

  <div class="ablock">
    <div class="alabel">Amount Contributed</div>
    <div class="aval"><span class="cur">£</span>{{ number_format($amount, 2) }}</div>
    <div class="adesc">by {{ $contributorName }} · {{ $groupName }}</div>
  </div>

  <div class="alert a-success">
    ✅ The contribution has been recorded on the shared ledger. All members can view the updated balance.
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/groups/{{ $groupId }}" class="btn-g">View Group Ledger</a>
  </div>
@endsection
