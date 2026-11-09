<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-left">
            {{ __('Pengelolaan Data Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-left">
                
                <div id="pesan" class="mb-4 font-bold text-left"></div>

                @can('kelola-buku')
                <div class="border border-gray-200 rounded-lg p-6 mb-8 bg-white shadow-sm text-left">
                    <h3 id="formTitle" class="font-bold text-lg text-gray-800 mb-4 text-left">Form Data Buku</h3>
                    
                    <form id="formBuku" class="space-y-4 text-left">
                        <input type="hidden" id="bukuId">

                        <div class="text-left">
                            <label class="block font-semibold text-gray-700 mb-1 text-left">Kategori ID:</label>
                            <input type="number" id="kategori_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border text-left" required>
                        </div>

                        <div class="text-left">
                            <label class="block font-semibold text-gray-700 mb-1 text-left">Judul Buku:</label>
                            <input type="text" id="judul" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border text-left" required>
                        </div>

                        <div class="text-left">
                            <label class="block font-semibold text-gray-700 mb-1 text-left">Penulis:</label>
                            <input type="text" id="penulis" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border text-left" required>
                        </div>

                        <div class="text-left">
                            <label class="block font-semibold text-gray-700 mb-1 text-left">Tahun Terbit:</label>
                            <input type="number" id="tahun_terbit" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border text-left" required>
                        </div>

                        <div class="pt-2 flex justify-start gap-2">
                            <button type="submit" id="btnSimpan" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow transition">
                                Simpan
                            </button>
                            <button type="button" id="btnBatal" class="px-5 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-md shadow transition hidden" onclick="resetForm()">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
                @endcan

                <h3 class="font-bold text-lg text-gray-800 mb-3 text-left">Daftar Buku</h3>
                
                <div class="overflow-x-auto text-left">
                    <table class="w-full border-collapse border border-gray-200 text-left">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-200 text-left text-gray-700 font-bold">
                                <th class="p-3 border border-gray-200 text-left">Kategori</th>
                                <th class="p-3 border border-gray-200 text-left">Judul</th>
                                <th class="p-3 border border-gray-200 text-left">Penulis</th>
                                <th class="p-3 border border-gray-200 text-left" style="width: 120px;">Tahun Terbit</th>
                                <th class="p-3 border border-gray-200 text-left" style="width: 250px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelBuku" class="text-left">
                            @php
                                $bukus = \App\Models\Buku::with('kategori')->get();
                            @endphp
                            @forelse($bukus as $buku)
                                <tr class="border-b border-gray-200 hover:bg-gray-50 text-left">
                                    <td class="p-3 border border-gray-200 text-left">
                                        {{ $buku->kategori->nama_kategori ?? $buku->kategori->nama ?? 'Umum' }}
                                    </td>
                                    <td class="p-3 border border-gray-200 font-medium text-left" id="judul-buku-{{ $buku->id }}">
                                       {{$buku->judul}}
                                    </td>
                                    <td class="p-3 border border-gray-200 text-left">{{ $buku->penulis }}</td>
                                    <td class="p-3 border border-gray-200 text-left">{{ $buku->tahun_terbit ?? $buku->tahun ?? '-' }}</td>
                                    <td class="p-3 border border-gray-200 text-left">
                                        <div class="flex items-center justify-start gap-2">
                                            <a href="/buku/{{ $buku->id }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded transition inline-block text-left" style="text-decoration: none;">Detail</a>
                                            
                                            @can('kelola-buku')
                                                <button type="button" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded transition text-left" onclick="editBuku({{ $buku->id }}, {{ $buku->kategori_id ?? 1 }}, '{{ addslashes($buku->judul) }}', '{{ addslashes($buku->penulis) }}', '{{ $buku->tahun_terbit ?? '-' }}')">Ubah</button>
                                                <button type="button" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded transition text-left" onclick="hapusBuku({{ $buku->id }})">Hapus</button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-3 border text-left text-gray-500">Belum ada data buku.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

<script>
        const API_URL = '/api/buku';
        const CSRF_TOKEN = @json(csrf_token());
        const ADALAH_SISWA = @json(auth()->check() && (auth()->user()->hasRole('Siswa') || auth()->user()->hasRole('siswa')));
        const BISA_KELOLA = @json(auth()->check() && auth()->user()->can('kelola-buku'));
        async function ambilDataBuku() {
            try {
                const response = await fetch(API_URL, {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Gagal mengambil data dari API');

                const result = await response.json();
                let listBuku = Array.isArray(result) ? result : (result.data || []);
                let rows = '';

                if (listBuku.length === 0) {
                    rows = `<tr><td colspan="5" class="p-3 border text-left text-gray-500">Belum ada data buku.</td></tr>`;
                } else {
                    listBuku.forEach(buku => {
                        let namaKategori = 'Umum';
                        if (buku.kategori) {
                            namaKategori = buku.kategori.nama_kategori || buku.kategori.nama || 'Kategori ' + buku.kategori_id;
                        }

                        let katId = buku.kategori_id || 1;
                        let tahun = buku.tahun_terbit || buku.tahun || buku.thn || '-';

                        let judulClean = String(buku.judul || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
                        let penulisClean = String(buku.penulis || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');

                        let tombolAksi = `
                            <a href="/buku/${buku.id}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded transition inline-block text-left" style="text-decoration: none;">Detail</a>
                        `;

                        if (ADALAH_SISWA) {
                            tombolAksi += `
                                <form action="{{ route('peminjaman.store') }}" method="POST" class="inline-block m-0">
                                    <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                                    <input type="hidden" name="buku_id" value="${buku.id}">
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded transition text-left">Pinjam</button>
                                </form>
                            `;
                        }

                        if (BISA_KELOLA) {
                            tombolAksi += `
                                <button type="button" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded transition text-left" onclick="editBuku(${buku.id}, ${katId}, '${judulClean}', '${penulisClean}', '${tahun}')">Ubah</button>
                                <form action="{{ url('/buku/' . $buku->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?');" style="display:inline;">
                                    <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="button" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded transition text-left" onclick="hapusBuku(${buku.id})">Hapus</button>
                                </form>
                            `;
                        }

                        rows += `
                            <tr class="border-b border-gray-200 hover:bg-gray-50 text-left">
                                <td class="p-3 border border-gray-200 text-left">${namaKategori}</td>
                                <td class="p-3 border border-gray-200 font-medium text-left" id="judul-buku-${buku.id}"></td>
                                <td class="p-3 border border-gray-200 text-left">${buku.penulis}</td>
                                <td class="p-3 border border-gray-200 text-left">${tahun}</td>
                                <td class="p-3 border border-gray-200 text-left">
                                    <div class="flex items-center justify-start gap-2">
                                        ${tombolAksi}
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }

                document.getElementById('tabelBuku').innerHTML = rows;

                listBuku.forEach(buku => {
                    const elJudul = document.getElementById(`judul-buku-${buku.id}`);
                    if (elJudul) {
                        elJudul.textContent = buku.judul; 
                    }
                });

            } catch (error) {
                tampilkanPesan(error.message, 'red');
            }
        }

        const formBuku = document.getElementById('formBuku');
        if (formBuku) {
            formBuku.addEventListener('submit', async function(e) {
                e.preventDefault();

                const id = document.getElementById('bukuId').value;
                const payload = {
                    kategori_id: document.getElementById('kategori_id').value,
                    judul: document.getElementById('judul').value,
                    penulis: document.getElementById('penulis').value,
                    tahun_terbit: document.getElementById('tahun_terbit').value,
                };

                const method = id ? 'PUT' : 'POST';
                const url = id ? `${API_URL}/${id}` : API_URL;

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        let pesanError = result.message || 'Terjadi kesalahan validasi';
                        if (result.errors) {
                            pesanError = Object.values(result.errors).flat().join('<br>');
                        }
                        throw new Error(pesanError);
                    }

                    tampilkanPesan(id ? 'Buku berhasil diperbarui!' : 'Buku berhasil ditambahkan!', 'green');
                    resetForm();
                    ambilDataBuku(); 
                } catch (error) {
                    tampilkanPesan(error.message, 'red');
                }
            });
        }

        function editBuku(id, kategori_id, judul, penulis, tahun) {
            document.getElementById('bukuId').value = id;
            document.getElementById('kategori_id').value = kategori_id;
            document.getElementById('judul').value = judul;
            document.getElementById('penulis').value = penulis;
            document.getElementById('tahun_terbit').value = tahun;

            document.getElementById('formTitle').innerText = 'Form Ubah Data Buku';
            document.getElementById('btnSimpan').innerText = 'Perbarui';
            document.getElementById('btnBatal').classList.remove('hidden');

            document.getElementById('formBuku').scrollIntoView({ behavior: 'smooth' });
        }

        async function hapusBuku(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus buku ini?')) return;

            try {
                const response = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: { 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Gagal menghapus data buku');

                tampilkanPesan('Buku berhasil dihapus!', 'green');
                ambilDataBuku(); 
            } catch (error) {
                tampilkanPesan(error.message, 'red');
            }
        }

        function resetForm() {
            if (!document.getElementById('formBuku')) return;
            document.getElementById('bukuId').value = '';
            document.getElementById('formBuku').reset();
            document.getElementById('formTitle').innerText = 'Form Data Buku';
            document.getElementById('btnSimpan').innerText = 'Simpan';
            document.getElementById('btnBatal').classList.add('hidden');
        }

        function tampilkanPesan(pesan, warna) {
            const el = document.getElementById('pesan');
            if (el) {
                el.style.color = warna === 'green' ? '#16a34a' : '#dc2626';
                el.innerHTML = pesan;
            }
        }

         ambilDataBuku();
    </script>
</x-app-layout>