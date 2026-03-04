<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_feature_id')->nullable()->constrained('map_features')->nullOnDelete();
            $table->string('nama');
            $table->enum('tipe', ['primer', 'sekunder', 'tersier'])->default('sekunder');
            $table->decimal('panjang_km', 8, 3)->nullable();
            $table->enum('kondisi', ['baik', 'sedang', 'rusak'])->default('baik');
            $table->string('penanggung_jawab')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salurans');
    }
};
