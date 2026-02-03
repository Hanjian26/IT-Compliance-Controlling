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

    /* Card utama form */
    .form-container {
      background-color: #ffffff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      width: 400px;

      /* Beri jarak atas dan bawah agar card tidak mepet */
      margin: 60px auto;
      /* Atas-Bawah = 60px, Kiri-Kanan otomatis center */
    }

    .form-container img {
      display: block;
      width: 100%;
      height: auto;
    }

    .form-container h2 {
      text-align: center;
      margin-bottom: 32px;
      color: #2b2d42;
      font-size: 24px;
      position: relative;
      display: inline-block;
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
      transition: background-color 0.3s ease;
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
      color: #a94442;
      font-size: 14px;
    }
  </style>
</head>

<body>

  <div class="form-container">
    <img src="{{ asset('indomaret.png') }}" alt="Logo">
    <h2>Daftar Akun</h2>

    @if(session('error'))
    <div class="alert">
      {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="alert" style="background-color:#ddffdd; border-left:6px solid #4CAF50; color:#4CAF50;">
      {{ session('success') }}
    </div>
    @endif

    <form id="registerForm" action="{{ url('/register') }}" method="POST" onsubmit="return validateForm()">
      @csrf
      <input type="text" name="nama" id="nama" placeholder="Nama Lengkap" required>
      <input type="text" name="nik" id="nik" placeholder="Nomor Induk Karyawan (NIK) - 10 digit" required>
      <select name="department" id="department" required>
        <option value="">-- Pilih Department --</option>
        @foreach($departments as $dept)
        <option value="{{ $dept->id }}">{{ $dept->department }}</option>
        @endforeach
      </select>


      <input type="password" name="password" id="password" placeholder="Password" required>
      <input type="email" name="email" id="email" placeholder="Email" required>
      <!-- 
      <select name="role" id="role" required>
        <option value="">-- Pilih Role --</option>
        <option value="User">User</option>
        <option value="Auditor">Auditor</option>
        <option value="Reviewer">Reviewer</option>
      </select> -->

      <select name="level" id="level" required>
        <option value="">-- Pilih Role --</option>
        <option value="1">Admin</option>
        <option value="2">User</option>
      </select>

      <button type="submit">Daftar</button>
    </form>
    <form action="{{url('/login') }}">
      <button type="submit">Login</button>
    </form>
  </div>

  <script>
    function validateForm() {
      const nama = document.getElementById('nama').value.trim();
      const nik = document.getElementById('nik').value.trim();
      const department = document.getElementById('department').value.trim();
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value.trim();
      // const role = document.getElementById('role').value;
      const level = document.getElementById('level').value;

      const nikRegex = /^[0-9]{10}$/; // Hanya 10 angka
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (nama === "") {
        alert("Gagal: Nama tidak boleh kosong!");
        return false;
      }

      if (!nikRegex.test(nik)) {
        alert("Gagal: NIK harus terdiri dari 10 angka!");
        return false;
      }

      if (!emailRegex.test(email)) {
        alert("Gagal: Format email tidak valid!");
        return false;
      }

      if (password.length < 6) {
        alert("Gagal: Password minimal 6 karakter!");
        return false;
      }

      if (level === "") {
        alert("Gagal: Silakan pilih Level!");
        return false;
      }

      return true;
    }
  </script>

</body>

</html>