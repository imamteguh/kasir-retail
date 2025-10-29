<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Imam Teguh',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'role' => 'super_admin',
        ]);

        // Tambah toko super admin
        \App\Models\Store::create([
            'user_id' => 1,
            'name' => 'Toko Super'
        ]);

        // Tambah subscription super admin
        \App\Models\Subscription::create([
            'store_id' => 1,
            'plan_id' => 3,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'active',
        ]);
    }
}
