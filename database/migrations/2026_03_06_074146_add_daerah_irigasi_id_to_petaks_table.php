<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petaks', function (Blueprint $table) {
            $table->foreignId('daerah_irigasi_id')
                ->nullable()
                ->after('map_feature_id')
                ->constrained('daerah_irigasis')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('petaks', function (Blueprint $table) {
            $table->dropForeign(['daerah_irigasi_id']);
            $table->dropColumn('daerah_irigasi_id');
        });
    }
};
