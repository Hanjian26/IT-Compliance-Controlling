@extends('layouts.app')

@section('title', 'IT Compliance')

@section('content')
<div style="margin-left: 20px;">
  <h1>Menu Utama</h1>
  <p>Selamat datang, {{ Auth::user()->nama }}</p>
  {{-- <div class="dashboard" style="text-align: center;">
    <img src="{{ asset('icons/dashboard.jpg') }}?v=2" alt="Logo Dashboard" width="800" height="450">
  </div> --}}
</div>
@endsection