<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('irrigation_data', function (Blueprint $table) {
            $table->float('suhu_max')->nullable()->after('tanggal');
            $table->float('suhu_min')->nullable()->after('suhu_max');
            $table->float('kelembaban')->nullable()->after('suhu_min');       // dalam %
            $table->float('kecepatan_angin')->nullable()->after('kelembaban'); // dalam m/s
            $table->float('radiasi_matahari')->nullable()->after('kecepatan_angin'); // dalam MJ/m²/hari
            $table->float('kc')->nullable()->after('radiasi_matahari');        // koefisien tanaman
        });
    }

    public function down(): void
    {
        Schema::table('irrigation_data', function (Blueprint $table) {
            $table->dropColumn([
                'suhu_max', 'suhu_min', 'kelembaban',
                'kecepatan_angin', 'radiasi_matahari', 'kc'
            ]);
        });
    }
};
