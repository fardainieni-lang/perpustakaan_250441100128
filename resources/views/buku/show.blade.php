<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Buku') }}
        </h2>
    </x-slot>

    @php
        $data = is_array($buku) ? $buku : (method_exists($buku, 'toArray') ? $buku->toArray(request()) : $buku);
        
        $idBuku = $data['id'] ?? $buku->id ?? '-';
        $judulBuku = $data['judul'] ?? $buku->judul ?? '-';
        $penulisBuku = $data['penulis'] ?? $buku->penulis ?? '-';
        $tahunTerbit = $data['tahun_terbit'] ?? $data['tahun'] ?? $buku->tahun_terbit ?? '-';
        $gambarBuku = $data['gambar'] ?? $buku->gambar ?? null;
        
        $kategoriBuku = $data['kategori'] ?? $buku->kategori->nama_kategori ?? $buku->kategori ?? '-';
        if (is_array($kategoriBuku)) {
            $kategoriBuku = $kategoriBuku['nama_kategori'] ?? $kategoriBuku['nama'] ?? '-';
        }
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($buku)
                    <div class="detail-box">
                        <div class="detail-content">
                            @if($gambarBuku)
                                <img src="{{ asset($gambarBuku) }}" alt="{{ $judulBuku }}" class="w-48 mb-4 rounded">
                            @endif
                            
                            <div class="detail-info space-y-2">
                                <p><strong>ID Buku:</strong> {{ $idBuku }}</p>
                                <p><strong>Judul Buku:</strong> {{ $judulBuku }}</p>
                                <p><strong>Penulis:</strong> {{ $penulisBuku }}</p>
                                <p><strong>Tahun Terbit:</strong> {{ $tahunTerbit }}</p>
                                <p><strong>Kategori:</strong> {{ $kategoriBuku }}</p>
                            </div>
                        </div>

                        <br>
                        <a href="{{ route('buku.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">&laquo; Kembali ke Daftar Buku</a>
                    </div>
                @else
                    <div class="alert alert-error text-red-600">
                        <h2 class="font-bold text-lg">Data Buku Tidak Ditemukan!</h2>
                        <p>Maaf, buku dengan ID yang Anda cari tidak tersedia dalam sistem.</p>
                        <br>
                        <a href="{{ route('buku.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">&laquo; Kembali ke Daftar Buku</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>