<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('size')->nullable()->after('capacity');
            $table->json('addons')->nullable()->after('image');
        });

        // Populate existing rooms with default values
        DB::table('rooms')->get()->each(function ($room) {
            $defaultAddons = [
                ['name' => 'Breakfast', 'price' => 50000, 'description' => 'Enable breakfast addon', 'type' => 'per_guest_per_night'],
                ['name' => 'Extra Bed', 'price' => 150000, 'description' => 'Enable extra bed', 'type' => 'per_night'],
                ['name' => 'Late Check-out', 'price' => 100000, 'description' => 'Enable late check-out', 'type' => 'flat_fee'],
            ];

            $size = ($room->capacity >= 4) ? 25 : 15;

            DB::table('rooms')
                ->where('id', $room->id)
                ->update([
                    'size' => $size,
                    'addons' => json_encode($defaultAddons),
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['size', 'addons']);
        });
    }
};
