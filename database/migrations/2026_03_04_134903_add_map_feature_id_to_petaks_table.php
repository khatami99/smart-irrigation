<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petaks', function (Blueprint $table) {
            $table->foreignId('map_feature_id')->nullable()->constrained('map_features')->nullOnDelete()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('petaks', function (Blueprint $table) {
            $table->dropForeign(['map_feature_id']);
            $table->dropColumn('map_feature_id');
        });
    }
};
