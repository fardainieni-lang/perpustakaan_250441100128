<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanPolicy
{
    /**
     * Tentukan apakah user bisa melihat detail peminjaman tertentu.
     */
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['Admin', 'Petugas', 'admin', 'petugas'])) {
            return true;
        }
        
        $role = strtolower(trim($user->role ?? ''));
        if (in_array($role, ['admin', 'petugas'])) {
            return true;
        }

        if ((int) $peminjaman->anggota_id === (int) $user->id) {
            return true;
        }

        if (isset($peminjaman->user_id) && (int) $peminjaman->user_id === (int) $user->id) {
            return true;
        }

        return false;
    }
}