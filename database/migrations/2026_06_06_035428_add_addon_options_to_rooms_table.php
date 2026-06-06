<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->boolean('allow_breakfast')->default(true)->after('image');
            $table->boolean('allow_extra_bed')->default(true)->after('allow_breakfast');
            $table->boolean('allow_late_checkout')->default(true)->after('allow_extra_bed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['allow_breakfast', 'allow_extra_bed', 'allow_late_checkout']);
        });
    }
};
