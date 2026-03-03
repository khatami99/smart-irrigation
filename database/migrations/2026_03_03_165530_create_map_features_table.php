<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('map_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_layer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('petak_id')->nullable()->constrained('petaks')->nullOnDelete();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->decimal('luas_manual', 10, 2)->nullable(); // ha, input manual
            $table->json('geojson'); // GeoJSON geometry object
            $table->string('warna', 7)->nullable(); // override layer color
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_features');
    }
};
