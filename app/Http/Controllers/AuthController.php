<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Spatie\Activitylog\Models\Activity;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('register');
    }

   public function register(Request $request)
{
    $request->validate([
        'nik' => 'required|numeric|digits:10|unique:users,nik',
        'nama' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'level' => 'required|numeric',
        'department' => 'required|string',
    ]);

    $user = User::create([
        'nik' => $request->nik,
        'nama' => $request->nama,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'level' => $request->level,
        'department' => $request->department,
    ]);

    // ✅ Log ke file laravel.log
    Log::info('User mendaftar', [
        'nik' => $user->nik,
        'nama' => $user->nama,
        'email' => $user->email,
      'level' => $user->level,
        'department' => $user->department,
        'ip' => $request->ip(),
        'waktu' => now()->toDateTimeString(),
    ]);

    // ✅ Log ke tabel activity_log (Spatie)
    activity('auth')
        ->causedBy($user)
        ->withProperties([
            'nik' => $user->nik,
            'nama' => $user->nama,
            'email' => $user->email,
            'level' => $user->level,
            'department' => $user->department,
            'ip' => $request->ip(),
        ])
        ->log('User registered');

    return redirect('/register')->with('success', 'Registrasi berhasil!');
}

    public function showLoginForm()
    {
        return view('index');
    }

   public function login(Request $request)
{
    $credentials = $request->only('nik', 'password');

    if (Auth::attempt($credentials)) {
        $user = auth()->user();

        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'nik' => $user->nik,
                'nama' => $user->nama,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('User logged in');

        // 🔑 Redirect berdasarkan level
        if ($user->level == 1) {
            // Admin
            return redirect()->route('admin.main_menu');
        } elseif ($user->level == 2) {
            // User
            return redirect()->route('user.main_menu');
        }

        // fallback kalau level tidak dikenali
        return redirect('/')->with('error', 'Role tidak dikenali.');
    }

    // Jika login gagal
    activity('auth')
        ->withProperties([
            'nik' => $request->nik,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ])
        ->log('Login failed');

    return redirect()->back()->with('error', 'NIK atau password salah.');
}


    public function mainMenu()
    {
        return view('main_menu');
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'nik' => $user->nik,
                'nama' => $user->nama,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('User logged out');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
