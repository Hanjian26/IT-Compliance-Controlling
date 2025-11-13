<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>IT Compliance</title>
  <link rel="icon" href="{{ asset('indomaret.png') }}" type="image/png">

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
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
      font-size: 24px;
      position: relative;
      display: inline-block;
    }

    .login-container h2::after {
      content: "";
      display: block;
      width: 105%;
      margin: 8px auto 0;
      height: 4px;
      background: linear-gradient(to right, #2b2d42, #8d99ae);
      border-radius: 2px;
    }

    .login-container form {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    #reg {
      margin-top: -10px;
    }

    .login-container input[type="text"],
    .login-container input[type="password"] {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    .login-container button {
      padding: 12px;
      background-color: #2b2d42;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      transition: background-color 0.3s ease;
    }

    .login-container button:hover {
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

  <div class="login-container">
    <h2>IT COMPLIANCE</h2>

    @if(session('error'))
    <div class="alert">
      {{ session('error') }}
    </div>
    @endif

    <form action="{{ url('/login') }}" method="POST">
      @csrf
      <input type="text" name="nik" placeholder="Nomor Induk Karyawan (NIK)" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

  </div>

</body>

</html>