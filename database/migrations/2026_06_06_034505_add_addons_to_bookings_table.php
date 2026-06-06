<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('include_breakfast')->default(false)->after('special_requests');
            $table->boolean('include_extra_bed')->default(false)->after('include_breakfast');
            $table->boolean('late_checkout')->default(false)->after('include_extra_bed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['include_breakfast', 'include_extra_bed', 'late_checkout']);
        });
    }
};
