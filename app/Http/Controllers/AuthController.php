<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department; // 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Spatie\Activitylog\Models\Activity;


class AuthController extends Controller
{
    // 🔹 Form Login
    public function showLoginForm()
    {
        return view('index'); // ubah ke 'login' kalau file view login kamu adalah login.blade.php
    }

    // 🔹 Form Register
    public function showRegisterForm()
    {
        // Ambil daftar department dari database
        $departments = Department::orderBy('department', 'asc')->get();

        return view('register', compact('departments'));
    }

    // 🔹 Proses Register
    public function register(Request $request)
    {
        $request->validate([
            'nik' => 'required|numeric|digits:10|unique:users,nik',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'level' => 'required|numeric',
            'department' => 'required|integer|exists:departments,id',

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

    // 🔹 Proses Login
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
                return redirect()->route('admin.main_menu');
            } elseif ($user->level == 2) {
                return redirect()->route('user.main_menu');
            }

            return redirect('/')->with('error', 'Role tidak dikenali.');
        }

        // Login gagal
        activity('auth')
            ->withProperties([
                'nik' => $request->nik,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Login failed');

        return redirect()->back()->with('error', 'NIK atau password salah.');
    }

    // 🔹 Menu utama
    public function mainMenu()
    {
        return view('main_menu');
    }

    // 🔹 Logout
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