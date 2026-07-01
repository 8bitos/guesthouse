<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Gallery;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure storage directories exist and copy dummy images
        Storage::disk('public')->makeDirectory('rooms');
        Storage::disk('public')->makeDirectory('gallery');

        $defaultImages = ['bedroom.png', 'pool.png', 'restaurant.png', 'villa.png'];
        foreach ($defaultImages as $img) {
            $sourcePath = public_path('images/default_gallery/'.$img);
            if (File::exists($sourcePath)) {
                $roomDest = Storage::disk('public')->path('rooms/'.$img);
                File::copy($sourcePath, $roomDest);

                $galleryDest = Storage::disk('public')->path('gallery/'.$img);
                File::copy($sourcePath, $galleryDest);
            }
        }

        // Seed Admin User
        User::updateOrCreate(
            ['email' => 'bagusguesthouse01@gmail.com'],
            [
                'name' => 'Admin Bagus',
                'password' => Hash::make('admin1234'),
                'role' => 'admin',
                'phone' => '+6282169911168',
                'address' => 'Jl. Majapahit Gg. Muria, Kuta, Bali',
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
                'type' => 'Deluxe Double Room',
                'price' => 910000,
                'capacity' => 4,
                'description' => 'Perfect for families with stunning mountain views, 1 King + 2 other beds, and modern amenities.',
                'status' => 'tersedia',
                'image' => 'rooms/bedroom.png',
            ],
            [
                'name' => 'Family Suite 2',
                'type' => 'Deluxe Double Room',
                'price' => 1040000,
                'capacity' => 4,
                'description' => 'Spacious family accommodation with separate living area, 1 King + 2 other beds.',
                'status' => 'tersedia',
                'image' => 'rooms/villa.png',
            ],
            [
                'name' => 'Suite 3',
                'type' => 'Superior King Room',
                'price' => 1200000,
                'capacity' => 2,
                'description' => 'Luxurious suite with premium furnishings, 1 King bed, and valley views.',
                'status' => 'tersedia',
                'image' => 'rooms/bedroom.png',
            ],
            [
                'name' => 'Suite 4',
                'type' => 'Superior King Room',
                'price' => 910000,
                'capacity' => 2,
                'description' => 'Comfortable suite ideal for couples and honeymooners, 1 King bed.',
                'status' => 'tersedia',
                'image' => 'rooms/villa.png',
            ],
            [
                'name' => 'Suite 5',
                'type' => 'Superior King Room',
                'price' => 1040000,
                'capacity' => 2,
                'description' => 'Modern suite with elegant design, 1 King bed, and mountain vistas.',
                'status' => 'tersedia',
                'image' => 'rooms/bedroom.png',
            ],
            [
                'name' => 'Potato Room 1',
                'type' => 'Standard Double Room',
                'price' => 650000,
                'capacity' => 2,
                'description' => 'Cozy shared bathroom room with valley views and 1 Queen bed.',
                'status' => 'tersedia',
                'image' => 'rooms/pool.png',
            ],
            [
                'name' => 'Potato Room 2',
                'type' => 'Budget Double Room',
                'price' => 650000,
                'capacity' => 2,
                'description' => 'Budget-friendly accommodation with shared facilities and 1 Queen bed.',
                'status' => 'tersedia',
                'image' => 'rooms/restaurant.png',
            ],
            [
                'name' => 'Potato Room 3',
                'type' => 'Budget Double Room',
                'price' => 650000,
                'capacity' => 2,
                'description' => 'Intimate room perfect for budget travelers with 1 Queen bed.',
                'status' => 'tersedia',
                'image' => 'rooms/pool.png',
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
            ['icon' => 'filter_hdr', 'title' => 'Mountain View', 'description' => 'Breathtaking views of surrounding mountains and Batur'],
            ['icon' => 'landscape', 'title' => 'Valley View', 'description' => 'Scenic valley panoramas and peaceful settings'],
            ['icon' => 'pool', 'title' => 'Swimming Pool', 'description' => 'Mountain-side infinity pool with panoramic views'],
            ['icon' => 'directions_bike', 'title' => 'Activities', 'description' => 'Hiking, trekking, jeep tours and adventures'],
            ['icon' => 'restaurant', 'title' => 'Fine Dining', 'description' => 'Restaurant and cafe with local cuisine'],
            ['icon' => 'spa', 'title' => 'Spa Services', 'description' => 'Wellness and relaxation treatments'],
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

        // Seed default gallery photos
        $galleries = [
            [
                'image' => 'gallery/bedroom.png',
                'caption' => 'Luxury Bedroom View',
                'order_index' => 1,
            ],
            [
                'image' => 'gallery/pool.png',
                'caption' => 'Infinity Pool View',
                'order_index' => 2,
            ],
            [
                'image' => 'gallery/restaurant.png',
                'caption' => 'Bamboo Restaurant',
                'order_index' => 3,
            ],
            [
                'image' => 'gallery/villa.png',
                'caption' => 'Resort Villa Exterior',
                'order_index' => 4,
            ],
        ];

        foreach ($galleries as $galleryData) {
            Gallery::updateOrCreate(
                ['image' => $galleryData['image']],
                $galleryData
            );
        }

        $this->call(BookingSeeder::class);
    }
}
