<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kebutuhan_air_di', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daerah_irigasi_id')->constrained('daerah_irigasis')->cascadeOnDelete();
            $table->foreignId('musim_tanam_id')->constrained('musim_tanams')->cascadeOnDelete();
            $table->foreignId('blangko_o01_id')->nullable()->constrained('blangko_o01s')->nullOnDelete();

            // Periode dekade
            $table->year('tahun');
            $table->unsignedTinyInteger('bulan');   // 1-12
            $table->enum('dekade', ['I', 'II', 'III']);

            // Input luas tanam (dari O-01)
            $table->decimal('luas_padi', 10, 2)->default(0);
            $table->decimal('luas_palawija', 10, 2)->default(0);
            $table->decimal('luas_tebu', 10, 2)->default(0);

            // Varietas padi yang dipakai untuk Kc
            $table->enum('varietas_padi', ['padi_unggul', 'padi_biasa'])->default('padi_unggul');

            // Data iklim agregat dekade (dari irrigation_data)
            $table->decimal('eto_dekade', 8, 3)->nullable();   // mm/hari rata-rata
            $table->decimal('ch_dekade', 8, 2)->nullable();    // mm total

            // Nilai Kc yang dipakai per komoditas
            $table->decimal('kc_padi', 4, 3)->nullable();
            $table->decimal('kc_palawija', 4, 3)->nullable();
            $table->decimal('kc_tebu', 4, 3)->nullable();

            // Hasil kalkulasi KP-01 (mm/hari)
            $table->decimal('etc_padi', 8, 3)->nullable();         // ETc = Kc × ETo
            $table->decimal('etc_palawija', 8, 3)->nullable();
            $table->decimal('etc_tebu', 8, 3)->nullable();
            $table->decimal('re_dekade', 8, 3)->nullable();        // Hujan efektif
            $table->decimal('nfr_padi', 8, 3)->nullable();         // NFR per komoditas
            $table->decimal('nfr_palawija', 8, 3)->nullable();
            $table->decimal('nfr_tebu', 8, 3)->nullable();

            // Hasil akhir kebutuhan air (lt/det)
            $table->decimal('kebutuhan_padi', 10, 3)->nullable();
            $table->decimal('kebutuhan_palawija', 10, 3)->nullable();
            $table->decimal('kebutuhan_tebu', 10, 3)->nullable();
            $table->decimal('kebutuhan_total', 10, 3)->nullable();  // Total lt/det per DI

            // Efisiensi yang dipakai
            $table->decimal('efisiensi', 4, 3)->nullable();        // 0.83 DIP / 0.65 DIR

            $table->timestamps();

            // Satu DI hanya punya satu record per dekade per MT
            $table->unique(
                ['daerah_irigasi_id', 'musim_tanam_id', 'tahun', 'bulan', 'dekade'],
                'unique_kad_per_dekade'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kebutuhan_air_di');
    }
};
