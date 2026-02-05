<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>IT Compliance & Controlling</title>
  <link rel="icon" href="{{ asset('indomaret.png') }}" type="image/png">

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 130vh;
      margin: 0;
    }

    .form-container {
      background-color: #ffffff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      width: 400px;
      margin: 60px auto;
    }

    .form-container img {
      display: block;
      width: 100%;
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 32px;
      color: #2b2d42;
      font-size: 24px;
      position: relative;
    }

    .form-container h2::after {
      content: "";
      display: block;
      width: 105%;
      margin: 8px auto 0;
      height: 4px;
      background: linear-gradient(to right, #2b2d42, #8d99ae);
      border-radius: 2px;
    }

    .form-container form {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .form-container input,
    .form-container select {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    .form-container button {
      padding: 12px;
      background-color: #2b2d42;
      color: white;
      border: none;
      border-radius: 8px;
      margin-top: 10px;
      font-size: 14px;
      cursor: pointer;
    }

    .form-container button:hover {
      background-color: #5763e1;
    }

    .alert {
      background-color: #ffdddd;
      border-left: 6px solid #f44336;
      padding: 10px;
      margin-bottom: 16px;
      border-radius: 6px;
      font-size: 14px;
    }
  </style>
</head>

<body>

  <div class="form-container">
    <img src="{{ asset('indomaret.png') }}" alt="Logo">
    <h2>Daftar Akun</h2>

    @if(session('error'))
    <div class="alert">{{ session('error') }}</div>
    @endif

    @if(session('success'))
    <div class="alert" style="background:#ddffdd;border-left:6px solid #4CAF50;color:#2e7d32">
      {{ session('success') }}
    </div>
    @endif

    <form action="{{ url('/register') }}" method="POST" onsubmit="return validateForm()">
      @csrf

      <input type="text" name="nama" id="nama" placeholder="Nama Lengkap" required>

      <input type="text" name="nik" id="nik" placeholder="Nomor Induk Karyawan (10 digit)" required>

      <select name="department" id="department" required>
        <option value="">-- Pilih Department --</option>
        @foreach($departments as $dept)
        <option value="{{ $dept->id }}">{{ $dept->department }}</option>
        @endforeach
      </select>

      <select name="level" id="level" required>
        <option value="">-- Pilih Level --</option>
        <option value="1">Admin</option>
        <option value="1">Atasan</option>
        <option value="2">Supervisor</option>
      </select>


      <input type="email" name="email" id="email" placeholder="Email" required>

      <input type="password" name="password" id="password" placeholder="Password" required>

      <button type="submit">Daftar</button>
    </form>


    <form action="{{ url('/login') }}">
      <button type="submit">Login</button>
    </form>
  </div>

  <script>
    function validateForm() {
      const nikRegex = /^[0-9]{10}$/;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!nikRegex.test(nik.value.trim())) {
        alert("NIK harus 10 digit angka!");
        return false;
      }

      if (!emailRegex.test(email.value.trim())) {
        alert("Format email tidak valid!");
        return false;
      }

      if (password.value.length < 6) {
        alert("Password minimal 6 karakter!");
        return false;
      }

      return true;
    }
  </script>

</body>

</html>