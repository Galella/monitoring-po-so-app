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
        Schema::create('cm_data', function (Blueprint $table) {
            $table->id();
            $table->string('ppcw')->nullable();
            $table->string('container')->nullable()->index();
            $table->string('seal')->nullable();
            $table->string('shipper')->nullable();
            $table->string('consignee')->nullable();
            $table->string('status')->nullable();
            $table->string('commodity')->nullable();
            $table->string('size')->nullable();
            $table->integer('weight')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('cm')->nullable()->index();
            $table->date('atd')->nullable();
            $table->string('no_order_coins')->nullable();
            $table->foreignId('area_id')->constrained('areas');
            $table->foreignId('imported_by')->constrained('users');
            $table->timestamps();

            // Composite index for matching
            $table->index(['cm', 'container'], 'cm_data_matching_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cm_data');
    }
};
