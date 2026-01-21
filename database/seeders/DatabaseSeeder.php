<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Hapus semua data yang ada
        DB::table('users')->delete();

        // Buat user admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $this->command->info('User admin berhasil dibuat!');
        $this->command->info('Username: admin');
        $this->command->info('Password: password123');
    }
}