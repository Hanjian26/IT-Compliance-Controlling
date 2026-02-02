<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="{{ asset('indomaret.png') }}?v=2" type="image/png">
  <title>@yield('title', 'IT Compliance & Controlling')</title>

  <style>
    /* (style tetap, tidak berubah – kamu bisa pisahkan ke file CSS jika mau) */
    body {
      margin: 0;
      font-family: sans-serif;
      background-color: #f8f8f8;
    }

    .sidebar {
      width: 230px;
      background: white;
      padding: 16px;
      border-right: 1px solid #eee;
      height: 100vh;
      overflow-y: auto;
      position: fixed;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .logo-text {
      font-size: 14px;
      font-weight: 600;
      white-space: nowrap;
    }

    .section-title {
      font-size: 12px;
      color: #777;
      margin: 10px 0 5px;
    }

    .menu-item,
    .dropdown-toggle,
    .sub-dropdown>.dropdown-toggle {
      display: flex;
      align-items: center;
      gap: 8px;
      background: none;
      border: none;
      padding: 10px;
      width: 100%;
      text-align: left;
      cursor: pointer;
      font-size: 14px;
      border-radius: 6px;
    }

    .menu-item:hover,
    .dropdown-toggle:hover,
    .sub-dropdown .dropdown-toggle:hover {
      background-color: #f0f0f0;
    }

    .active {
      background-color: #e5e5e5;
      border-radius: 6px;
    }

    .dropdown-content {
      display: none;
      flex-direction: column;
      padding-left: 16px;
    }

    .dropdown-content a::before {
      content: "•";
      margin-right: 8px;
      color: #999;
      font-size: 12px;
    }

    .dropdown-content a:hover {
      cursor: pointer;
      background-color: #e9e2e2
    }

    .sub-dropdown .dropdown-content {
      border-left: 2px solid #e0e0e0;
      margin-left: 10px;
      padding-left: 12px;
    }

    .dropdown-content a {
      padding: 6px 0;
      text-decoration: none;
      color: #333;
      font-size: 13px;
    }

    .arrow {
      margin-left: auto;
      transition: transform 0.3s ease;
    }

    .icon {
      width: 20px;
      display: inline-block;
    }

    .user-info {
      margin-bottom: 20px;
      font-size: 13px;
      color: #333;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
    }

    .topbar {
      width: calc(100% - 230px);
      height: 60px;
      background-color: #ffffff;
      border-bottom: 1px solid #ccc;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      padding: 0 0px;
      position: fixed;
      top: 0;
      left: 263px;
      z-index: 999;
    }

    .topbar-content {
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }

    .welcome-text {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      padding-right: 100px;
    }

    .logout-button {
      width: 100%;
      padding: 10px;
      background-color: #e74c3c;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      margin-top: 20px;
    }

    .logout-button:hover {
      background-color: #c0392b;
    }

    .content {
      margin-left: 230px;
      padding: 80px 20px 20px 20px;
    }
  </style>

  @stack('styles')
</head>

<body>
  {{-- Sidebar --}}
  <aside class="sidebar">
    <div class="logo">
      <img src="{{ asset('indomaret.png') }}?v=2" alt="Logo" class="logo-icon" style="width: 100px; height: 30px;">
      <span class="logo-text">IT COMPLIANCE & CONTROLLING</span>
    </div>

    <nav>
      <p class="section-title">MAIN</p>

      <a href="#" class="menu-item">
        <span class="icon">🏠</span> Dashboard
      </a>

      <div class="menu-dropdown">
        <button class="dropdown-toggle">
          <span class="icon">📁</span> Dokumen ITC
          <span class="arrow">▾</span>
        </button>
        <div class="dropdown-content">
          <a href="">Memo Kebijakan</a>
          <a href="#">Memo Administrasi</a>
          <a href="#">Memo Permintaan Data</a>
          <a href="#">Memo Audit</a>
          <a href="#">Memo Penemuan</a>
          <a href="#">TBA</a>
        </div>
      </div>

      <div class="menu-dropdown">
        <button class="dropdown-toggle">
          <span class="icon">🧾</span> Audit
          <span class="arrow">▾</span>
        </button>
        <div class="dropdown-content">
          <div class="sub-dropdown">
            <button class="dropdown-toggle">Audit Backup & Restore ▾</button>
            <div class="dropdown-content">
              <a href="#">Laporan Hasil Akhir (LHA)</a>
              <a href="#">Working Paper (WP)</a>
              <a href="#">Release Laporan Hasil Akhir</a>
            </div>
          </div>
        </div>
      </div>

      <div class="menu-dropdown">
        <button class="dropdown-toggle">
          <span class="icon">📁</span> Format Dokumen
          <span class="arrow">▾</span>
        </button>
        <div class="dropdown-content">
          <a href="#">Berita Acara (BA)</a>
          <a href="#">User Acceptance (UAT)</a>
          <a href="#">Scope of Work (SoW)</a>
          <a href="#">Skenario Testing</a>
          <a href="#">Minute of Meeting (MoM)</a>
          <a href="#">Working Paper (WP)</a>
          <a href="#">Laporan Hasil Akhir (LHA)</a>
        </div>
      </div>

      {{-- Logout --}}
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-button">Logout</button>
      </form>
    </nav>
  </aside>

  {{-- Topbar --}}
  {{-- Topbar --}}
  <header class="topbar">
    <div class="topbar-content">
      @auth
      <span class="welcome-text">Selamat Datang, {{ Auth::user()->nama }}</span>

      @else
      <span class="welcome-text">Selamat Datang, Tamu</span>
      @endauth
    </div>

  </header>


  {{-- Main Content --}}
  <main class="content">
    @yield('content')
  </main>

  <script src="{{ asset('script.js') }}"></script>
  @stack('scripts')
</body>

</html>