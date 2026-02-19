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
        Schema::create('irrigation_data', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->float('eto');
            $table->float('etc');
            $table->float('curah_hujan');
            $table->float('kebutuhan_air');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('irrigation_data');
    }
};
