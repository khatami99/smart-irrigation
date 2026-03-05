<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daerah_irigasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_feature_id')->nullable()->constrained('map_features')->nullOnDelete();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->decimal('luas_total', 10, 2)->nullable();
            $table->string('sumber_air')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daerah_irigasis');
    }
};
