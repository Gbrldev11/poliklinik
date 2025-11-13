<?php

namespace App\Http\Controllers;

use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalPeriksaController extends Controller
{
    /**
     * Menampilkan daftar semua jadwal periksa untuk dokter yang sedang login (READ)
     */
    public function index()
    {
        // 1. Ambil user (dokter) dari Auth
        $dokter = Auth::user();

        // 2. Ambil id_dokter dan ambil semua jadwal yang terkait, diurutkan berdasarkan hari
        $jadwalPeriksas = JadwalPeriksa::where('id_dokter', $dokter->id)->orderBy('hari')->get();

        // 3. Kembalikan view dengan data jadwal
        return view('dokter.jadwal-periksa.index', compact('jadwalPeriksas'));
    }

    /**
     * Menampilkan form untuk membuat jadwal periksa baru (CREATE - Form)
     */
    public function create()
    {
        return view('dokter.jadwal-periksa.create');
    }

    /**
     * Menyimpan jadwal periksa baru ke database (CREATE - Proses)
     */
    public function store(Request $request)
    {
        // Validasi data yang masuk
        $request->validate([ 
            'hari' => 'required', 
            'jam_mulai' => 'required', 
            'jam_selesai' => 'required'
        ]); 

        // Simpan data ke tabel jadwal_periksas
        JadwalPeriksa::create([ 
            'id_dokter' => Auth::id(), 
            'hari' => $request->hari, 
            'jam_mulai' => $request->jam_mulai, 
            'jam_selesai' => $request->jam_selesai 
        ]); 

        // Redirect kembali ke halaman index dengan notifikasi sukses
        return redirect()->route('jadwal-periksa.index') 
            ->with('message', 'Data Berhasil di Simpan') 
            ->with('type', 'success'); 
    }

    /**
     * Menampilkan form untuk mengedit jadwal periksa tertentu (UPDATE - Form)
     */
    public function edit($id)
    {
        // Cari data jadwal berdasarkan ID atau gagal
        $jadwalPeriksa = JadwalPeriksa::findOrFail($id); 

        // Kembalikan view edit dengan data jadwal yang ditemukan
        return view('dokter.jadwal-periksa.edit', compact('jadwalPeriksa')); 
    }

    /**
     * Memperbarui jadwal periksa di database (UPDATE - Proses)
     */
    public function update(Request $request, string $id)
    {
        // Validasi data yang masuk
        $request->validate([ 
            'hari' => 'required', 
            'jam_mulai' => 'required', 
            'jam_selesai' => 'required' 
        ]); 

        // Cari data jadwal berdasarkan ID atau gagal
        $jadwalPeriksa = JadwalPeriksa::findOrFail($id); 

        // Perbarui data
        $jadwalPeriksa->update([ 
            'hari' => $request->hari, 
            'jam_mulai' => $request->jam_mulai, 
            'jam_selesai' => $request->jam_selesai 
        ]); 

        // Redirect kembali ke halaman index dengan notifikasi sukses
        return redirect()->route('jadwal-periksa.index') 
            ->with('message', 'Berhasil Melakukan Update Data') 
            ->with('type', 'success'); 
    }

    /**
     * Menghapus jadwal periksa dari database (DELETE)
     */
    public function destroy(string $id)
    {
        // Cari data jadwal berdasarkan ID atau gagal
        $jadwalPeriksa = JadwalPeriksa::findOrFail($id);

        // Hapus data
        $jadwalPeriksa->delete();

        // Redirect kembali ke halaman index dengan notifikasi sukses
        return redirect()->route('jadwal-periksa.index')
            ->with('message', 'Berhasil Melakukan Hapus Data')
            ->with('type', 'success');
    }
}