@extends('emails.layouts.email')

@section('subject', 'Payout Received — ' . $groupName)
@section('eyebrow', 'Payout')
@section('icon', '🎊')
@section('icon_style', 'ic-gold')
@section('title', 'Payout Received!')
@section('subtitle', 'Your turn has come — congratulations!')

@section('content')
  <p>Hello,</p>
  <p>Congratulations! Your payout from <strong>"{{ $groupName }}"</strong> has been processed and recorded on the ledger.</p>

  <div class="ablock">
    <div class="alabel">Payout Amount</div>
    <div class="aval"><span class="cur">£</span>{{ number_format($amount, 2) }}</div>
    <div class="adesc">from {{ $groupName }}</div>
  </div>

  <div class="icard">
    <div class="ilabel">Payout Summary</div>
    <div class="irow">
      <span class="ilb">Group</span>
      <span class="ivl">{{ $groupName }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Amount</span>
      <span class="ivl gold">£{{ number_format($amount, 2) }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Status</span>
      <span class="ivl green">Processed</span>
    </div>
  </div>

  <div class="alert a-success">
    🏆 Your consistency and commitment paid off. Well done for staying on track every cycle!
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/groups/{{ $groupId }}" class="btn-g">View Group Details</a>
  </div>
@endsection
