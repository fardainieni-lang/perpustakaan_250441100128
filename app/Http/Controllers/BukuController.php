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

        // Jika diakses via AJAX/API, kembalikan JSON
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
            'kategori'     => $bukuModel->kategori->nama ?? '-', // Mengambil nama kategorinya
            'gambar'       => $bukuModel->gambar,
        ];

        return view('buku.show', compact('buku'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'judul'        => 'required',
            'penulis'      => 'required',
            'tahun_terbit' => 'nullable',
            'kategori_id'  => 'nullable',
        ]);

        $buku = \App\Models\Buku::create([
            'judul'        => $request->judul,
            'penulis'      => $request->penulis,
            'tahun_terbit' => $request->tahun_terbit ?? 2024,
            'kategori_id'  => $request->kategori_id ?? 1,
        ]);

        return response()->json(['message' => 'Buku berhasil ditambahkan!', 'data' => $buku], 200);
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'judul'        => 'required',
            'penulis'      => 'required',
            'tahun_terbit' => 'nullable',
            'kategori_id'  => 'nullable',
        ]);

        $buku = \App\Models\Buku::findOrFail($id);
        $buku->update([
            'judul'        => $request->judul,
            'penulis'      => $request->penulis,
            'tahun_terbit' => $request->tahun_terbit ?? 2024,
            'kategori_id'  => $request->kategori_id ?? 1,
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
