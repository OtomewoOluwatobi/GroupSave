@extends('emails.layouts.email')

@section('subject', 'New Lead Submitted — ' . config('app.name'))
@section('eyebrow', 'Admin Alert')
@section('icon', '📋')
@section('icon_style', 'ic-violet')
@section('title', 'New Lead Submitted')
@section('subtitle', 'A new lead just came in')

@section('content')
  <p>Hello <strong>{{ $adminName }}</strong>,</p>
  <p>A new lead has been submitted via the waitlist/survey form.</p>

  <div class="icard">
    <div class="ilabel">Lead Details</div>
    <div class="irow">
      <span class="ilb">Name</span>
      <span class="ivl">{{ $lead->name }}</span>
    </div>
    <div class="irow">
      <span class="ilb">Email</span>
      <span class="ivl">{{ $lead->email }}</span>
    </div>
    @if($lead->location)
    <div class="irow">
      <span class="ilb">Location</span>
      <span class="ivl">{{ $lead->location }}</span>
    </div>
    @endif
    @if($lead->source)
    <div class="irow">
      <span class="ilb">Source</span>
      <span class="ivl">{{ $lead->source }}</span>
    </div>
    @endif
    @if($lead->cooking_habit)
    <div class="irow">
      <span class="ilb">Cooking Habit</span>
      <span class="ivl">{{ $lead->cooking_habit }}</span>
    </div>
    @endif
    @if($lead->grocery_frequency)
    <div class="irow">
      <span class="ilb">Grocery Frequency</span>
      <span class="ivl">{{ $lead->grocery_frequency }}</span>
    </div>
    @endif
    @if($lead->hassle_score !== null)
    <div class="irow">
      <span class="ilb">Hassle Score</span>
      <span class="ivl gold">{{ $lead->hassle_score }}/10</span>
    </div>
    @endif
    @if($lead->likelihood_score !== null)
    <div class="irow">
      <span class="ilb">Likelihood Score</span>
      <span class="ivl gold">{{ $lead->likelihood_score }}/10</span>
    </div>
    @endif
    @if($lead->fee_pref)
    <div class="irow">
      <span class="ilb">Fee Preference</span>
      <span class="ivl">{{ $lead->fee_pref }}</span>
    </div>
    @endif
    @if($lead->delivery_pref)
    <div class="irow">
      <span class="ilb">Delivery Preference</span>
      <span class="ivl">{{ $lead->delivery_pref }}</span>
    </div>
    @endif
    @if($lead->pain_points)
    <div class="irow" style="flex-direction:column;align-items:flex-start;gap:4px;">
      <span class="ilb">Pain Points</span>
      <span class="ivl" style="margin-left:0;">{{ $lead->pain_points }}</span>
    </div>
    @endif
  </div>

  <div class="btn-wrap">
    <a href="{{ config('app.frontend_url') }}/admin/leads/{{ $lead->id }}" class="btn-g">View in Admin Panel</a>
  </div>
@endsection
