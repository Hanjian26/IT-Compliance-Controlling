@extends('layouts.app')

@section('title', 'IT Compliance')

@section('content')
  <div style="margin-left: 20px;">
    <h1>Menu Utama</h1>
    <p>Selamat datang, {{ Auth::user()->nama }}</p>
  </div>
@endsection
