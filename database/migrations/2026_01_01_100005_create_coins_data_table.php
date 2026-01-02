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
        Schema::create('coins_data', function (Blueprint $table) {
            $table->id();
            $table->string('cm')->nullable()->index();
            $table->string('order')->nullable();
            $table->string('container')->nullable()->index();
            $table->string('seal')->nullable();
            $table->string('size_20')->nullable();
            $table->string('size_40')->nullable();
            $table->string('no_po')->nullable();
            $table->string('kereta')->nullable();
            $table->date('atd')->nullable();
            $table->string('customer')->nullable();
            $table->string('stasiun_asal')->nullable();
            $table->string('stasiun_tujuan')->nullable();
            $table->string('gudang_asal')->nullable();
            $table->string('gudang_tujuan')->nullable();
            $table->string('jenis')->nullable();
            $table->string('service')->nullable();
            $table->string('payment')->nullable();
            $table->string('so')->nullable();
            $table->date('submit_so')->nullable();
            $table->bigInteger('nominal_ppn')->nullable();
            $table->bigInteger('sa_ppn')->nullable();
            $table->bigInteger('loading_ppn')->nullable();
            $table->bigInteger('unloading_ppn')->nullable();
            $table->bigInteger('t_orig_ppn')->nullable();
            $table->bigInteger('t_dest_ppn')->nullable();
            $table->bigInteger('sa')->nullable();
            $table->bigInteger('loading')->nullable();
            $table->bigInteger('unloading')->nullable();
            $table->bigInteger('t_orig')->nullable();
            $table->bigInteger('t_dest')->nullable();
            $table->bigInteger('nominal')->nullable();
            $table->bigInteger('klaim')->nullable();
            $table->string('dokumen_klaim')->nullable();
            $table->string('alur')->nullable();
            $table->string('dokumen')->nullable();
            $table->integer('berat')->nullable();
            $table->text('isi_barang')->nullable();
            $table->string('ppcw')->nullable();
            $table->string('owner')->nullable();
            $table->foreignId('wilayah_id')->constrained('wilayahs');
            $table->foreignId('imported_by')->constrained('users');
            $table->timestamps();

            // Composite index for matching
            $table->index(['cm', 'container'], 'coins_data_matching_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coins_data');
    }
};
