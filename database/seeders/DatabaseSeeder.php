<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Admin User
        User::updateOrCreate(
            ['email' => 'admin@guesthouse.com'],
            [
                'name' => 'Admin Bagus',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+6282169911168',
                'address' => 'Batur, Kintamani, Bali',
            ]
        );

        // Seed Regular Customer (Pelanggan) User
        User::updateOrCreate(
            ['email' => 'user@guesthouse.com'],
            [
                'name' => 'Tamu Bagus',
                'password' => Hash::make('password'),
                'role' => 'pelanggan',
                'phone' => '+6281234567890',
                'address' => 'Denpasar, Bali',
            ]
        );

        // Seed Rooms
        $rooms = [
            [
                'name' => 'Family Suite 1',
                'type' => 'Family Suite',
                'price' => 910000,
                'capacity' => 4,
                'description' => 'Perfect for families with stunning mountain views, 1 King + 2 other beds, and modern amenities.',
                'status' => 'tersedia',
            ],
            [
                'name' => 'Family Suite 2',
                'type' => 'Family Suite',
                'price' => 1040000,
                'capacity' => 4,
                'description' => 'Spacious family accommodation with separate living area, 1 King + 2 other beds.',
                'status' => 'tersedia',
            ],
            [
                'name' => 'Suite 3',
                'type' => 'Suite',
                'price' => 1200000,
                'capacity' => 2,
                'description' => 'Luxurious suite with premium furnishings, 1 King bed, and valley views.',
                'status' => 'tersedia',
            ],
            [
                'name' => 'Suite 4',
                'type' => 'Suite',
                'price' => 910000,
                'capacity' => 2,
                'description' => 'Comfortable suite ideal for couples and honeymooners, 1 King bed.',
                'status' => 'tersedia',
            ],
            [
                'name' => 'Suite 5',
                'type' => 'Suite',
                'price' => 1040000,
                'capacity' => 2,
                'description' => 'Modern suite with elegant design, 1 King bed, and mountain vistas.',
                'status' => 'tersedia',
            ],
            [
                'name' => 'Potato Room 1',
                'type' => 'Budget',
                'price' => 650000,
                'capacity' => 2,
                'description' => 'Cozy shared bathroom room with valley views and 1 Queen bed.',
                'status' => 'tersedia',
            ],
            [
                'name' => 'Potato Room 2',
                'type' => 'Budget',
                'price' => 650000,
                'capacity' => 2,
                'description' => 'Budget-friendly accommodation with shared facilities and 1 Queen bed.',
                'status' => 'tersedia',
            ],
            [
                'name' => 'Potato Room 3',
                'type' => 'Budget',
                'price' => 650000,
                'capacity' => 2,
                'description' => 'Intimate room perfect for budget travelers with 1 Queen bed.',
                'status' => 'tersedia',
            ],
        ];

        foreach ($rooms as $roomData) {
            Room::updateOrCreate(
                ['name' => $roomData['name']],
                $roomData
            );
        }

        // Seed default facilities
        $facilities = [
            ['icon' => '🏔️', 'title' => 'Mountain View', 'description' => 'Breathtaking views of surrounding mountains and Batur'],
            ['icon' => '🏞️', 'title' => 'Valley View', 'description' => 'Scenic valley panoramas and peaceful settings'],
            ['icon' => '🏊', 'title' => 'Swimming Pool', 'description' => 'Mountain-side infinity pool with panoramic views'],
            ['icon' => '🚴', 'title' => 'Activities', 'description' => 'Hiking, trekking, jeep tours and adventures'],
            ['icon' => '🍽️', 'title' => 'Fine Dining', 'description' => 'Restaurant and cafe with local cuisine'],
            ['icon' => '💆', 'title' => 'Spa Services', 'description' => 'Wellness and relaxation treatments'],
        ];

        foreach ($facilities as $facilityData) {
            Facility::updateOrCreate(
                ['title' => $facilityData['title']],
                $facilityData
            );
        }

        // Seed default CMS settings
        Setting::setValue('about_title', 'About Us');
        Setting::setValue('about_desc', 'Experience luxury hospitality with breathtaking mountain and valley views. Bagus Guest House offers modern facilities, comfortable accommodations, and world-class dining in a serene natural setting.');
        Setting::setValue('about_why_list', "Spectacular mountain and valley views\nModern luxury accommodations\nWorld-class dining and cafe\nProfessional and friendly staff");
        Setting::setValue('about_vision', 'To be the most preferred luxury accommodation destination in the region, offering unforgettable experiences and exceptional hospitality.');
    }
}
