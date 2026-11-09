<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use App\Http\Resources\BukuResource; // Impor BukuResource
use Illuminate\Http\Request;

class BukuController extends Controller
{
  public function index(Request $request)
    {
        $query = Buku::with('kategori')->withCount('peminjamans')->latest();

        if ($request->has('cari')) {
            $cari = $request->cari;
            $query->where(function($q) use ($cari) {
                $q->where('judul', 'like', '%' . $cari . '%')
                ->orWhere('penulis', 'like', '%' . $cari . '%');
            });
        }

        if ($request->has('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->has('tahun')) {
            $query->where('tahun_terbit', $request->tahun);
        }

        // 1. Jika URL yang diakses berawalan /api/ atau meminta format JSON (Fetch JS / Postman)
        if ($request->is('api/*') || $request->wantsJson() || $request->expectsJson()) {
            $bukus = $query->paginate(10)->withQueryString();
            return BukuResource::collection($bukus);
        }

        // 2. Jika diakses langsung via URL browser biasa (/buku)
        return view('buku.index');
    }

    public function store(StoreBukuRequest $request)
    {
        $buku = Buku::create($request->validated()); 

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data'    => new BukuResource($buku->load('kategori'))
        ], 201);
    }

   public function show(Request $request, $id)
    {
        // 1. Ambil data buku murni dari Database beserta relasi kategorinya
        $buku = Buku::with('kategori')->findOrFail($id);

        // 2. Jika dipanggil dari API / Postman / Fetch JS (Modul 4), kembalikan BukuResource
        if ($request->is('api/*') || $request->wantsJson()) {
            return new BukuResource($buku);
        }

        // 3. Jika dipanggil dari Browser (Web Blade Modul 5), kirimkan Model Eloquent murni
        return view('buku.show', compact('buku'));
    }

    public function update(UpdateBukuRequest $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'not found'], 404);
        }

        $buku->update($request->validated());

        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data'    => new BukuResource($buku->load('kategori'))
        ], 200);
    }

    public function destroy($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'not found'], 404);
        }

        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus'], 200);
    }

    public function rekapBukuPerKategori()
    {
        $rekap = Kategori::withCount('bukus')->get();

        return response()->json([
            'data' => $rekap
        ], 200);
    }

    public function rekapPeminjamanPerBuku()
    {
        $rekap = Buku::withCount('peminjamans')->get();

        return response()->json([
            'data' => $rekap
        ], 200);
    }
}