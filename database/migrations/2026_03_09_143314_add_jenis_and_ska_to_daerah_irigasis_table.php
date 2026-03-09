<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daerah_irigasis', function (Blueprint $table) {
            // Jenis DI: permukaan (DIP) atau rawa (DIR)
            $table->enum('jenis', ['permukaan', 'rawa'])->default('permukaan')->after('kode');

            // Parameter SKA — berlaku untuk DIP & DIR
            // Nilai default sesuai Lampiran Permen PU No. 32/PRT/M/2007
            $table->decimal('ska_padi_pengolahan',  5, 3)->default(1.250)->after('jenis');
            $table->decimal('ska_padi_pertumbuhan', 5, 3)->default(0.725)->after('ska_padi_pengolahan');
            $table->decimal('ska_palawija_banyak',  5, 3)->default(0.300)->after('ska_padi_pertumbuhan');
            $table->decimal('ska_palawija_sedikit', 5, 3)->default(0.200)->after('ska_palawija_banyak');

            // Parameter khusus DIP (Irigasi Permukaan)
            $table->decimal('faktor_tersier', 4, 3)->default(0.830)->after('ska_palawija_sedikit');

            // Parameter khusus DIR (Irigasi Rawa)
            $table->decimal('pct_kehilangan_air', 5, 2)->default(35.00)->after('faktor_tersier');
        });
    }

    public function down(): void
    {
        Schema::table('daerah_irigasis', function (Blueprint $table) {
            $table->dropColumn([
                'jenis',
                'ska_padi_pengolahan',
                'ska_padi_pertumbuhan',
                'ska_palawija_banyak',
                'ska_palawija_sedikit',
                'faktor_tersier',
                'pct_kehilangan_air',
            ]);
        });
    }
};
