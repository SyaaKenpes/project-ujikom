<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Menampilkan halaman profil
    public function index()
    {
        return view('profile');
    }

    // Memproses update data diri & foto profil
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'         => 'required|string|max:255',
            'no_hp'        => 'required|string|max:15',
            'alamat'       => 'required|string',
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name'   => $request->name,
            'no_hp'  => $request->no_hp,
            'alamat' => $request->alamat,
        ];

        // Jika user mengunggah foto profil baru
        if ($request->hasFile('foto_profile')) {
            // Hapus foto lama jika ada
            if ($user->foto_profile && file_exists(public_path('images/profiles/' . $user->foto_profile))) {
                unlink(public_path('images/profiles/' . $user->foto_profile));
            }

            $file = $request->file('foto_profile');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/profiles'), $filename);
            
            $data['foto_profile'] = $filename;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // Memproses ganti password dengan verifikasi password lama
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi!',
            'password.min'              => 'Password baru minimal harus 6 karakter!',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok!',
        ]);

        $user = Auth::user();

        // Cek apakah password lama yang dimasukkan sudah benar
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan salah!']);
        }

        // Update ke password baru yang di-hash
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}