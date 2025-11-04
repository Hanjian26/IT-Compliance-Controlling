<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="{{ asset('indomaret.png') }}?v=2" type="image/png">
    <title>@yield('title', 'IT Compliance')</title>

    <style>
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
    .dropdown-toggle {
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
    .dropdown-toggle:hover {
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
        left: 262px;
        z-index: 999;
    }

    .welcome-text {
        padding-right: 100px;
        font-size: 14px;
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
            <img src="{{ asset('indomaret.png') }}?v=2" alt="Logo" class="logo-icon"
                style="width: 100px; height: 30px;">
            <span class="logo-text">IT COMPLIANCE</span>
        </div>

        @php
        $user = Auth::user();
        $level = $user->level ?? null;
        $levelMap = [1 => 'Admin', 2 => 'User'];
        $levelName = $levelMap[$level] ?? 'Unknown';
        @endphp

        <nav>
            <p class="section-title">Main Menu</p>

            {{-- Dashboard --}}
            <a href="{{ $level == 1 ? route('admin.main_menu') : route('user.main_menu') }}" class="menu-item">
                <span class="icon">
                    <img width="20" height="20" src="https://img.icons8.com/ios/50/home--v1.png" alt="home" />
                </span> Dashboard
            </a>

            {{-- Menu khusus Admin --}}
            @if ($level == 1)
            <div class="menu-dropdown">
                <button class="dropdown-toggle">
                    <span class="icon">
                        <img width="20" height="20" src="https://img.icons8.com/ios/50/document-in-folder.png" />
                    </span>
                    Dokumen ITC
                    <span class="arrow">▾</span>
                </button>
                <div class="dropdown-content">
                    <a href="{{ route('memo.index') }}">Memo Kebijakan</a>
                    <a href="{{ route('memo.administrasi') }}">Memo Administrasi</a>
                    <a href="{{ route('memo.permintaan.data') }}">Memo Permintaan Data</a>
                    <a href="{{ route('memo.audit') }}">Memo Audit</a>
                    <a href="{{ route('memo.penemuan') }}">Memo Penemuan</a>
                </div>
            </div>
            @endif

            {{-- Menu Audit --}}
            <div class="menu-dropdown">
                <button class="dropdown-toggle">
                    <span class="icon">
                        <img width="20" height="20" src="https://img.icons8.com/ios/50/fine-print--v1.png"
                            alt="audit" />
                    </span>
                    Audit
                    <span class="arrow">▾</span>
                </button>
                <div class="dropdown-content">
                    @if($level == 2)
                    <a href="{{ route('audit.lha') }}">Laporan Hasil Audit</a>
                    @else
                    <a href="{{ route('audit.brdb') }}">Jadwal Audit Backup & Restore Database</a>
                    <a href="{{ route('audit.wp') }}">Working Paper</a>
                    <a href="{{ route('audit.lha') }}">Laporan Hasil Audit</a>
                    <a href="{{ route('audit.tlha') }}">Tindak Lanjut Hasil Audit</a>
                    @endif
                </div>
            </div>

            {{-- Template Dokumen --}}
            <a href="{{ route('template.dokumen') }}" class="menu-item">
                <span class="icon">
                    <img width="20" height="20" src="https://img.icons8.com/ios/50/template.png" />
                </span> Template Dokumen
            </a>

            {{-- Admin only: Register User --}}
            @if ($level == 1)
            <a href="{{ url('/register') }}" target="_blank" class="menu-item">
                <span class="icon">
                    <img width="20" height="20" src="https://img.icons8.com/ios/50/add-user-male.png" />
                </span> Daftarkan Akun
            </a>
            @endif

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </nav>
    </aside>


    {{-- Topbar --}}
    <header class="topbar">
        <div class="topbar-content">
            @auth
            @php
            $levelMap = [1 => 'Admin', 2 => 'User'];
            $levelName = $levelMap[$level] ?? 'Unknown';
            @endphp
            <span class="welcome-text">Selamat Datang, {{ $user->nama }} ({{ $levelName }})</span>
            @endauth
        </div>
    </header>

    {{-- Konten --}}
    <main class="content">
        @yield('content')
    </main>

    {{-- Dropdown JavaScript --}}
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggles = document.querySelectorAll(".dropdown-toggle");

        toggles.forEach(toggle => {
            toggle.addEventListener("click", function() {
                const content = this.nextElementSibling;
                const arrow = this.querySelector(".arrow");

                if (content.style.display === "flex") {
                    content.style.display = "none";
                    arrow.style.transform = "rotate(0deg)";
                } else {
                    content.style.display = "flex";
                    arrow.style.transform = "rotate(180deg)";
                }
            });
        });
    });
    </script>

    @stack('scripts')
</body>

</html>