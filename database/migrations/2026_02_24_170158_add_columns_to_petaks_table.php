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
        Schema::table('petaks', function (Blueprint $table) {
            $table->string('kode_petak', 20)->unique();
            $table->string('nama_petak');
            $table->decimal('luas_area', 8, 2);
            $table->string('lokasi_wilayah');
            $table->string('pintu_air')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petaks', function (Blueprint $table) {
            //
        });
    }
};
