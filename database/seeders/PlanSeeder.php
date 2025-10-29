<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Plan::create([
            'name' => 'Basic',
            'price' => 20000,
            'duration_days' => 30,
            'description' => 'Plan Basic'
        ]);
        
        \App\Models\Plan::create([
            'name' => 'Pro',
            'price' => 75000,
            'duration_days' => 120,
            'description' => 'Plan Pro'
        ]);

        \App\Models\Plan::create([
            'name' => 'Enterprise',
            'price' => 350000,
            'duration_days' => 365,
            'description' => 'Plan Enterprise'
        ]);
    }
}
