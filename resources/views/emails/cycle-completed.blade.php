@extends('emails.layouts.email')

@section('subject', 'Cycle Completed — ' . $groupName)
@section('eyebrow', 'Cycle Complete')
@section('icon', '🏆')
@section('icon_style', 'ic-green')
@section('title', 'Cycle Completed!')
@section('subtitle', 'Another milestone reached together')

@section('content')
  <p>Hello <strong>{{ $userName }}</strong>,</p>
  <p>Cycle <strong>#{{ $cycleNumber }}</strong> for <strong>"{{ $groupName }}"</strong> has been completed successfully. Every member fulfilled their commitment — that's the power of community savings.</p>

  <div class="icard">
    <div class="ilabel">Cycle Summary</div>
    <div class="irow">
      <span class="ilb">Group</span>
      <span class="ivl">{{ $groupName }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Cycle Number</span>
      <span class="ivl">#{{ $cycleNumber }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Status</span>
      <span class="ivl green">Completed ✓</span>
    </div>
  </div>

  <div class="alert a-success">
    🎉 <strong>Well done to all members!</strong> Every contribution was made on time this cycle.
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/groups/{{ $groupId }}" class="btn-g">View Group</a>
  </div>
@endsection
