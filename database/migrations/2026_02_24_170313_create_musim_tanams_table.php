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
        Schema::table('musim_tanams', function (Blueprint $table) {
            $table->string('nama_mt');
            $table->enum('jenis_mt', ['MT1', 'MT2', 'MT3', 'MK']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('target_luas_tanam', 10, 2);
            $table->string('jenis_tanaman')->default('Padi');
            $table->enum('status', ['rencana', 'berjalan', 'selesai'])->default('rencana');
            $table->text('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musim_tanams');
    }
};
