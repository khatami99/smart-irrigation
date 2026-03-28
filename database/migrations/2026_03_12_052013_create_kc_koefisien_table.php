<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kc_koefisien', function (Blueprint $table) {
            $table->id();

            // Jenis komoditas: padi_unggul, padi_biasa, palawija, tebu
            $table->string('komoditas', 20);

            // Fase ke-berapa (1, 2, 3, dst) sejak awal MT
            $table->unsignedTinyInteger('fase_ke');

            // Nama fase (untuk label tampilan)
            $table->string('nama_fase', 50);

            // Nilai Kc
            $table->decimal('kc', 4, 3);

            // Durasi fase dalam dekade
            $table->unsignedTinyInteger('durasi_dekade');

            $table->timestamps();

            // Satu komoditas hanya boleh punya satu nilai Kc per fase
            $table->unique(['komoditas', 'fase_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kc_koefisien');
    }
};
