<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. BUAT USER-NYA DULU (Ini WAJIB dieksekusi pertama)
        User::factory(500)->create();

        // 2. SETELAH USER ADA, BARU KITA BUAT POSTINGANNYA
        \App\Models\Post::factory(300)->create();
    }
}
