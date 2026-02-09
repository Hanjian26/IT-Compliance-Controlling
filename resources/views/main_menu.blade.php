@extends('layouts.app')

@section('title', 'IT Compliance & Controlling')

@section('content')

<style>
  .pin-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #f2f4f8;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }

  .login-container {
    background-color: #ffffff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    width: 340px;
  }

  .login-container h2 {
    text-align: center;
    margin-bottom: 32px;
    color: #2b2d42;
    font-size: 18px;
  }

  .login-container h2::after {
    content: "";
    display: block;
    width: 100%;
    margin: 10px auto 0;
    height: 4px;
    background: linear-gradient(to right, rgb(0, 0, 0), rgb(0, 0, 0), rgb(5, 5, 0));
    border-radius: 2px;
  }

  .login-container form {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .login-container input {
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    text-align: center;
  }

  .login-container button {
    padding: 12px;
    background-color: #2b2d42;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
  }

  .login-container button:hover {
    background-color: #5763e1;
  }

  .alert {
    background-color: #ffdddd;
    border-left: 6px solid #f44336;
    padding: 10px;
    border-radius: 6px;
    color: #a94442;
    font-size: 14px;
  }
</style>

<div style="margin-left: 20px; text-align: center;">
  <img src="{{ asset('home.png') }}?v=2" style="width: 70%; display: block; margin: 0 auto;">
</div>

@if(!auth()->user()->owned_pin)
<div class="pin-overlay">

  <div class="login-container">
    <img src="{{ asset('indomaret.png') }}" alt="Logo" style="width:120px;display:block;margin:0 auto 10px;">

    <h2>BUAT PIN KEAMANAN</h2>

    @if($errors->any())
    <div class="alert">
      {{ $errors->first('pin') }}
    </div>
    @endif

    <form action="{{ route('pin.store') }}" method="POST">
      @csrf

      <input type="password" name="pin" maxlength="6" pattern="[0-9]*" inputmode="numeric" placeholder="PIN 6 digit"
        required>

      <input type="password" name="pin_confirmation" maxlength="6" pattern="[0-9]*" inputmode="numeric"
        placeholder="Konfirmasi PIN" required>

      <button type="submit">Simpan PIN</button>

      <p style="margin:0 0 1px 0; line-height:1.2;">
        <strong>Note:</strong>
      </p>
      <ul style="margin:0; padding-left:20px; line-height:1.2; list-style-type:disc;">
        <li style="margin-bottom:2px; font-size:14px;">PIN hanya berupa angka</li>
        <li style="margin-bottom:2px; font-size:14px;">Angka berurutan 6 digit tidak diperbolehkan</li>
        <li style="margin-bottom:2px; font-size:14px;">Angka kembar 6 digit tidak diperbolehkan</li>
        <li style="margin-bottom:0; font-size:14px;">PIN tidak bisa menggunakan huruf atau simbol</li>
      </ul>

    </form>
  </div>

</div>
@endif

@endsection