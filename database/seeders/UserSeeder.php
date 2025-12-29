<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'nama' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin1234'),
                'role' => 'admin',
            ],
            [
                'nama' => 'Dokter',
                'email' => 'dokter@gmail.com',
                'password' => Hash::make('dokter123'),
                'role' => 'dokter',
            ],
        ];
        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']], // cek berdasarkan email
                $user
            );
        }
    }
}
