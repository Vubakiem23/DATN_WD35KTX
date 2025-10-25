<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->enum('location_type', ['phong', 'kho'])->default('phong')->after('tai_san_id');
            $table->unsignedBigInteger('location_id')->nullable()->after('location_type'); // id phòng nếu có
        });
    }

    public function down(): void
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->dropColumn(['location_type', 'location_id']);
        });
    }
};
