<?php
// database/migrations/xxxx_create_rtts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rtts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petak_id')->constrained('petaks')->onDelete('cascade');
            $table->foreignId('musim_tanam_id')->constrained('musim_tanams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Jadwal tanam
            $table->date('rencana_mulai_tanam');
            $table->date('rencana_selesai_tanam');
            $table->date('realisasi_mulai_tanam')->nullable();
            $table->date('realisasi_selesai_tanam')->nullable();

            // Target & realisasi luas
            $table->decimal('target_luas', 8, 2);
            $table->decimal('realisasi_luas', 8, 2)->nullable();

            // Rotasi air — urutan giliran irigasi
            $table->integer('urutan_rotasi')->default(1);
            $table->integer('durasi_pemberian_air')->default(10); // lama giliran air (hari)

            // Fase pertumbuhan (JSON array jadwal fase)
            // Format: [{"fase":"pengolahan_tanah","mulai":"2025-11-01","selesai":"2025-11-10"}, ...]
            $table->json('jadwal_fase')->nullable();

            $table->enum('status', ['rencana', 'berjalan', 'selesai', 'terlambat'])->default('rencana');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Satu RTT per petak per MT
            $table->unique(['petak_id', 'musim_tanam_id'], 'unique_rtt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rtts');
    }
};
