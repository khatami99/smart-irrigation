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
        Schema::create('ai_predictions', function (Blueprint $table) {
            $table->id();
            $table->decimal('prediksi', 8, 4);
            $table->decimal('r2', 8, 4)->nullable();
            $table->decimal('rmse', 8, 4)->nullable();
            $table->string('status')->default('ok'); // ok / error / insufficient_data
            $table->text('pesan')->nullable();
            $table->timestamp('trained_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_predictions');
    }
};
