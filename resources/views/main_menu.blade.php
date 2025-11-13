@extends('layouts.app')

@section('title', 'IT Compliance')

@section('content')
<div style="margin-left: 20px; text-align: center;">
  {{-- <h1>Menu Utama</h1>
  <p>Selamat datang, {{ Auth::user()->nama }}</p> --}}
  <img src="{{ asset('home.png') }}?v=2" alt="main-menu" style="width: 70%; display: block; margin: 0 auto;">
</div>

@endsection