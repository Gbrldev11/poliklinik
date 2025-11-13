<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'dokter') {
                return redirect()->route('dokter.dashboard');
            } else {
                return redirect()->route('pasien.dashboard');
            }
        }

        return back()->withErrors(['email' => 'Email atau Password salah!']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama'      => ['required', 'string', 'max:255'],
            'alamat'    => ['required', 'string', 'max:255'],
            'no_hp'     => ['required', 'string', 'max:13'],
            'no_ktp'    => ['required', 'string', 'max:255', 'unique:users,no_ktp'],
            'email'     => ['required', 'string', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'confirmed'],
        ]);

        // Cek apakah nomor KTP sudah terdaftar
        if (User::where('no_ktp', $request->no_ktp)->exists()) {
            return back()->withErrors(['no_ktp' => 'Nomor KTP sudah terdaftar!']);
        }

        // Membuat nomor rekam medis (no_rm)
        $no_rm = date('ym') . str_pad(
            User::whereRaw('month(created_at) = month(now())')->count() + 1,
            3,
            '0',
            STR_PAD_LEFT
        );

        // Simpan data user baru
        User::create([
            'nama'      => $request->nama,
            'alamat'    => $request->alamat,
            'no_hp'     => $request->no_hp,
            'no_ktp'    => $request->no_ktp,
            'no_rm'     => $no_rm,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'pasien',
        ]);

        return redirect()->route('login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function dokter()
    {
        $data = Poli::with('dokters')->get();
        return $data;
    }
}
