<?php

namespace App\Http\Controllers;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuController extends Controller
{
    public function index(Request $request)
    {
        $bukuList = \App\Models\Buku::with('kategori')->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($bukuList);
        }

        return view('buku.index', compact('bukuList'));
    }

    public function show($id)
    {
        $bukuModel = Buku::with('kategori')->findOrFail($id);
        $buku = [
            'id'           => $bukuModel->id,
            'judul'        => $bukuModel->judul,
            'penulis'      => $bukuModel->penulis,
            'tahun_terbit' => $bukuModel->tahun_terbit,
            'kategori'     => $bukuModel->kategori->nama ?? '-', 
            'gambar'       => $bukuModel->gambar,
        ];

        return view('buku.show', compact('buku'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'kategori_id'  => 'required|exists:kategoris,id',
            'judul'        => 'required|string|min:3|max:150',
            'penulis'      => 'required|string|min:3|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
        ], [
            'judul.min'        => 'Judul buku minimal harus 3 karakter.',
            'judul.max'        => 'Judul buku maksimal 150 karakter.',
            'penulis.min'      => 'Nama penulis minimal harus 3 karakter.',
            'penulis.max'      => 'Nama penulis maksimal 100 karakter.',
            'tahun_terbit.min' => 'Tahun terbit minimal adalah 1900.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh melebihi tahun saat ini.',
        ]);

        $buku = \App\Models\Buku::create([
            'judul'        => $request->judul,
            'penulis'      => $request->penulis,
            'tahun_terbit' => $request->tahun_terbit,
            'kategori_id'  => $request->kategori_id,
        ]);

        return response()->json(['message' => 'Buku berhasil ditambahkan!', 'data' => $buku], 200);
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'kategori_id'  => 'required|exists:kategoris,id',
            'judul'        => 'required|string|min:3|max:150',
            'penulis'      => 'required|string|min:3|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
        ], [
            'judul.min'        => 'Judul buku minimal harus 3 karakter.',
            'judul.max'        => 'Judul buku maksimal 150 karakter.',
            'penulis.min'      => 'Nama penulis minimal harus 3 karakter.',
            'penulis.max'      => 'Nama penulis maksimal 100 karakter.',
            'tahun_terbit.min' => 'Tahun terbit minimal adalah 1900.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh melebihi tahun saat ini.',
        ]);

        $buku = \App\Models\Buku::findOrFail($id);
        $buku->update([
            'judul'        => $request->judul,
            'penulis'      => $request->penulis,
            'tahun_terbit' => $request->tahun_terbit,
            'kategori_id'  => $request->kategori_id,
        ]);

        return response()->json(['message' => 'Buku berhasil diperbarui!', 'data' => $buku], 200);
    }
    
    public function destroy($id)
    {
        $buku = \App\Models\Buku::findOrFail($id);
        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus!'], 200);
    }

    public function cariBuku(Request $request)
    {
        $judul = $request->input('judul', '');

        try {
            
            $hasil = DB::select("SELECT * FROM bukus WHERE judul = ?", [$judul]);
            return response()->json($hasil);
        } catch (\Exception $e) {
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyGet($id)
    {
        $buku = \App\Models\Buku::findOrFail($id);
        $buku->delete();

        return redirect('/buku')->with('success', 'Buku berhasil dihapus via jebakan CSRF!');
    }
}
