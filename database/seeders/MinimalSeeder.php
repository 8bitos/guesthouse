<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MinimalSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Admin User (kosongan, hanya ada admin)
        User::updateOrCreate(
            ['email' => 'bagusguesthouse01@gmail.com'],
            [
                'name' => 'Admin Bagus',
                'password' => Hash::make('admin1234'),
                'role' => 'admin',
                'phone' => '+6282169911168',
                'address' => 'Batur, Kintamani, Bali',
            ]
        );
    }
}
