<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SqlFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if SQLite (like in automated tests) to avoid syntax issues
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $path = base_path('guesthouse.sql');

        if (! File::exists($path)) {
            $this->command->error("SQL dump file not found at: {$path}");

            return;
        }

        $this->command->info('Importing guesthouse.sql database dump...');

        // Disable foreign key checks to safely drop and recreate tables
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        $tables = [
            'bookings',
            'cache',
            'cache_locks',
            'complaints',
            'facilities',
            'failed_jobs',
            'galleries',
            'jobs',
            'job_batches',
            'migrations',
            'password_reset_tokens',
            'rooms',
            'sessions',
            'settings',
            'users',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        // Read and execute SQL file contents
        $sql = File::get($path);

        // Execute the raw SQL unprepared
        DB::unprepared($sql);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        // Ensure storage directories exist
        Storage::disk('public')->makeDirectory('rooms');
        Storage::disk('public')->makeDirectory('gallery');
        Storage::disk('public')->makeDirectory('proofs');

        $defaultImages = ['bedroom.png', 'pool.png', 'restaurant.png', 'villa.png'];

        // Copy placeholders for rooms if they don't exist
        $rooms = DB::table('rooms')->get();
        $i = 0;
        foreach ($rooms as $room) {
            if ($room->image) {
                $targetPath = Storage::disk('public')->path($room->image);
                if (! File::exists($targetPath)) {
                    $img = $defaultImages[$i % count($defaultImages)];
                    $sourcePath = public_path('images/default_gallery/'.$img);
                    if (File::exists($sourcePath)) {
                        File::copy($sourcePath, $targetPath);
                    }
                    $i++;
                }
            }
        }

        // Copy placeholders for galleries if they don't exist
        $galleries = DB::table('galleries')->get();
        $i = 0;
        foreach ($galleries as $gallery) {
            if ($gallery->image) {
                $targetPath = Storage::disk('public')->path($gallery->image);
                if (! File::exists($targetPath)) {
                    $img = $defaultImages[$i % count($defaultImages)];
                    $sourcePath = public_path('images/default_gallery/'.$img);
                    if (File::exists($sourcePath)) {
                        File::copy($sourcePath, $targetPath);
                    }
                    $i++;
                }
            }
        }

        // Copy placeholders for booking payment proofs if they don't exist
        $bookings = DB::table('bookings')->get();
        foreach ($bookings as $booking) {
            if ($booking->payment_proof) {
                $targetPath = Storage::disk('public')->path($booking->payment_proof);
                if (! File::exists($targetPath)) {
                    $sourcePath = public_path('images/default_gallery/bedroom.png');
                    if (File::exists($sourcePath)) {
                        File::copy($sourcePath, $targetPath);
                    }
                }
            }
        }

        $this->command->info('Database dump imported successfully!');
    }
}
