<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blangko_o01s', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daerah_irigasi_id')->constrained('daerah_irigasis')->onDelete('cascade');
            $table->foreignId('musim_tanam_id')->constrained('musim_tanams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Luas usulan (dari Juru/Petani)
            $table->decimal('luas_padi_usulan', 10, 2)->default(0);
            $table->decimal('luas_palawija_usulan', 10, 2)->default(0);
            $table->decimal('luas_tebu_usulan', 10, 2)->default(0);

            // Luas disetujui (oleh Komisi Irigasi / Dinas)
            $table->decimal('luas_padi_disetujui', 10, 2)->nullable();
            $table->decimal('luas_palawija_disetujui', 10, 2)->nullable();
            $table->decimal('luas_tebu_disetujui', 10, 2)->nullable();

            // Status persetujuan
            $table->enum('status', ['usulan', 'disetujui', 'revisi'])->default('usulan');

            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Satu O-01 per DI per MT
            $table->unique(['daerah_irigasi_id', 'musim_tanam_id'], 'unique_o01');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blangko_o01s');
    }
};
