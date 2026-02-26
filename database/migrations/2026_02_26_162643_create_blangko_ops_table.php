<?php
// database/migrations/xxxx_create_blangko_ops_table.php
// Jalankan: php artisan make:migration create_blangko_ops_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blangko_ops', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('petak_id')->constrained('petaks')->onDelete('cascade');
            $table->foreignId('musim_tanam_id')->constrained('musim_tanams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // yang input

            // Periode dekade
            $table->integer('tahun');
            $table->integer('bulan');         // 1-12
            $table->enum('dekade', ['I', 'II', 'III']); // I=1-10, II=11-20, III=21-akhir bulan

            // Debit air
            $table->decimal('debit_rencana', 8, 2)->nullable();   // liter/detik
            $table->decimal('debit_realisasi', 8, 2)->nullable();  // liter/detik

            // Tinggi muka air
            $table->decimal('tinggi_muka_air', 6, 2)->nullable();  // cm

            // Luas areal
            $table->decimal('luas_rencana', 8, 2)->nullable();     // ha
            $table->decimal('luas_realisasi', 8, 2)->nullable();   // ha

            // Fase pertumbuhan
            $table->enum('fase_pertumbuhan', [
                'pengolahan_tanah',
                'tanam',
                'vegetatif',
                'generatif',
                'panen',
                'bero',
            ])->nullable();

            // Kondisi saluran & bangunan
            $table->enum('kondisi_saluran', ['baik', 'rusak_ringan', 'rusak_berat'])->nullable();
            $table->enum('kondisi_bangunan', ['baik', 'rusak_ringan', 'rusak_berat'])->nullable();
            $table->text('catatan_kondisi')->nullable();

            // Curah hujan dekade
            $table->decimal('curah_hujan', 6, 1)->nullable(); // mm

            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Satu catatan per petak per dekade per MT
            $table->unique(['petak_id', 'musim_tanam_id', 'tahun', 'bulan', 'dekade'], 'unique_blangko');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blangko_ops');
    }
};
