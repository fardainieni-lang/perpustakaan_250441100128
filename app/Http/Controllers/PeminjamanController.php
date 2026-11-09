<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Anggota;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:bukus,id',
        ]);

        
        Peminjaman::create([
            'anggota_id'      => auth()->id(), 
            'buku_id'         => $request->buku_id,
            'tanggal_pinjam'  => now()->toDateString(),
            'tanggal_kembali' => null,
        ]);

        return redirect()->route('peminjaman.riwayat')->with('success', 'Berhasil melakukan peminjaman buku!');
    }
   
    public function index()
    {
        $peminjamans = Peminjaman::with(['anggota', 'buku'])->latest()->get();
        return view('peminjaman.index', compact('peminjamans'));
    }

   
    public function riwayat()
    {
        $user = auth()->user();
        
        
        $peminjamans = Peminjaman::with('buku')
            ->where('anggota_id', $user->id)
            ->latest()
            ->get();

        return view('peminjaman.riwayat', compact('peminjamans'));
    }

    
    public function updateStatus(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update([
            'tanggal_kembali' => now()->toDateString(),
        ]);

        return back()->with('success', 'Buku telah berhasil dikembalikan!');
    }

    
    public function laporan()
    {
        
        $peminjamans = Peminjaman::with(['anggota', 'buku'])->latest()->get();

       
        $totalPinjam = $peminjamans->count();
        $sedangDipinjam = $peminjamans->whereNull('tanggal_kembali')->count();
        $sudahKembali = $peminjamans->whereNotNull('tanggal_kembali')->count();

        return view('laporan.index', compact('peminjamans', 'totalPinjam', 'sedangDipinjam', 'sudahKembali'));
    }

    public function show($id)
    {
        $peminjaman = \App\Models\Peminjaman::with('anggota')->findOrFail($id);
        if (\Illuminate\Support\Facades\Gate::denies('view', $peminjaman)) {
            abort(403, 'Unauthorized action. Anda tidak berhak melihat data ini.');
        }
        return view('peminjaman.show', compact('peminjaman'));
    }
}