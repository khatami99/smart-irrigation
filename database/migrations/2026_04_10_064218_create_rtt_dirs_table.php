<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rtt_dirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petak_id')->constrained('petaks')->onDelete('cascade');
            $table->foreignId('musim_tanam_id')->constrained('musim_tanams')->onDelete('cascade');
            $table->foreignId('daerah_irigasi_id')->constrained('daerah_irigasis')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->decimal('rencana_padi', 8, 2)->default(0);
            $table->decimal('realisasi_padi', 8, 2)->default(0);
            $table->decimal('rencana_palawija', 8, 2)->default(0);
            $table->decimal('realisasi_palawija', 8, 2)->default(0);
            $table->decimal('rencana_tanaman_keras', 8, 2)->default(0);
            $table->decimal('realisasi_tanaman_keras', 8, 2)->default(0);
            $table->decimal('rencana_bera', 8, 2)->default(0);
            $table->decimal('realisasi_bera', 8, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['petak_id', 'musim_tanam_id', 'bulan', 'tahun'], 'unique_rtt_dir');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rtt_dirs');
    }
};
