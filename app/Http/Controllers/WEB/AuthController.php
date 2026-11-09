<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('login');
    }

    // Memproses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan Role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'petugas') {
                return redirect()->route('petugas.peminjaman.index');
            } elseif ($user->role === 'peminjam') {
                return redirect()->route('peminjam.katalog.index');
            }

            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak dikenali.');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function authenticate(Request $request)
    {
        // 1. Validasi input form
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Cari user berdasarkan email di database
        $user = User::where('email', $request->email)->first();

        // 3. Jika akun TIDAK ADA di database
        if (!$user) {
            return back()->with('error', 'Akun tidak ditemukan. Silakan hubungi Admin atau buat akun baru.');
        }

        // 4. Jika akun ADA, cek kecocokan password
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Login berhasil, buat session baru
            $request->session()->regenerate();
            
            // Arahkan ke dashboard (sesuaikan dengan rute lu)
            return redirect()->intended('/admin/dashboard'); 
        }

        // 5. Jika akun ada tapi PASSWORD SALAH
        return back()->with('error', 'Password yang Anda masukkan salah.');
    }

    // ==========================================
    // TAMBAHAN: FITUR REGISTRASI PEMINJAM
    // ==========================================

    // Menampilkan form register
    public function showRegister()
    {
        return view('auth.register'); 
    }

    // Memproses data register
    public function storeRegister(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'no_hp'    => 'required|string|max:15',
            'alamat'   => 'required|string',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'peminjam', // Otomatis dikunci jadi peminjam
            'no_hp'    => $request->no_hp,
            'alamat'   => $request->alamat,
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil, silakan login!');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}