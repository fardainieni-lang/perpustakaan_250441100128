<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnggotaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (User::all() as $user) {
            Anggota::updateOrCreate(
                ['email' => $user->email],
                [
                    'id'         => $user->id,
                    'nama'       => $user->name,
                    'email'      => $user->email,
                    'no_telepon' => '08123456789',
                ]
            );
        }
    }
}