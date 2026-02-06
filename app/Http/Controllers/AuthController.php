<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;

class AuthController extends Controller
{
    /* =========================
     * SHOW LOGIN
     * ========================= */
    public function showLoginForm()
    {
        return view('index');
    }

    /* =========================
     * LOGIN PROCESS
     * ========================= */
    public function login(Request $request)
    {
        $request->validate([
            'nik'      => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('nik', 'password');

       if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->level == 1) {
            return redirect()->route('admin.main_menu');
        }

        if ($user->level == 2) {
            return redirect()->route('user.main_menu');
        }
    }

        return back()->withErrors([
            'nik' => 'NIK atau password salah',
        ]);
    }

    /* =========================
     * MAIN MENU (ADMIN & USER)
     * ========================= */
 public function mainMenu()
{
return view('main_menu');
}

    /* =========================
     * SHOW REGISTER
     * ========================= */
    public function showRegisterForm()
    {
    $departments = Department::orderBy('department')->get();
    $manager_id = User::where('level', 1)
    ->where('is_manager', 1)
    ->orderBy('nama')
    ->get();

    return view('register', compact('departments', 'manager_id'));
    }

    /* =========================
     * REGISTER PROCESS
     * ========================= */
  public function register(Request $request)
{
    $request->validate([
        'nik'        => 'required|unique:users,nik',
        'nama'       => 'required',
        'department' => 'required',
        'email'      => 'required|email|unique:users,email',
        'password'   => 'required|min:6',
        'level'      => 'required|in:1,2',
        'manager_id' => 'nullable|exists:users,nik',
    ]);

    User::create([
        'nik'        => $request->nik,
        'nama'       => $request->nama,
        'department' => $request->department, // ← 19
        'email'      => $request->email,
        'password'   => bcrypt($request->password),
        'level'      => $request->level,
        'manager_id' => $request->manager_id,
    ]);

    return redirect('/login')->with('success', 'User berhasil dibuat');
}


    /* =========================
     * LOGOUT
     * ========================= */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}