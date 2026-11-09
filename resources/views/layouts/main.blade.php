<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan AQU</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="header">
        <h1>Perpustakaan AQU</h1>
    </header>

    <nav class="navbar">
        <!-- 1. Beranda & Daftar Buku (Sudah Ada) -->
        <a href="{{ route('dashboard') }}">Beranda</a>
        <a href="{{ route('buku.index') }}">Daftar Buku</a>

        <!-- 2. Profil Saya (Bawaan Breeze - Sesuai Ketentuan Siswa) -->
        <a href="{{ route('profile.edit') }}">Profil Saya</a>

        <!-- 3. Riwayat Peminjaman Milik Sendiri (Khusus Role Siswa) -->
        @hasrole('Siswa')
            @if(Route::has('peminjaman.riwayat'))
                <a href="{{ route('peminjaman.riwayat') }}">Riwayat Peminjaman</a>
            @endif
        @endhasrole

        <!-- 4. Form Logout -->
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" style="background: none; border: none; color: white; font-weight: bold; cursor: pointer; margin-left: 15px;">
                Logout ({{ Auth::user()->name }})
            </button>
        </form>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <footer class="footer">
        <p>&copy; {{ date('Y') }} Perpustakaan AQU. All rights reserved.</p>
    </footer>
</body>
</html>