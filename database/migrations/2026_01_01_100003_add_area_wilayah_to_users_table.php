<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('area_id')->nullable()->after('remember_token')->constrained('areas')->nullOnDelete();
            $table->foreignId('wilayah_id')->nullable()->after('area_id')->constrained('wilayahs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropForeign(['wilayah_id']);
            $table->dropColumn(['area_id', 'wilayah_id']);
        });
    }
};
