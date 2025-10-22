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
        Schema::table('hoa_don', function (Blueprint $table) {
            // Đảm bảo Doctrine DBAL được cài (composer require doctrine/dbal)
            $table->unsignedBigInteger('sinh_vien_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoa_don', function (Blueprint $table) {
            $table->unsignedBigInteger('sinh_vien_id')->nullable(false)->change();
        });
    }
};
