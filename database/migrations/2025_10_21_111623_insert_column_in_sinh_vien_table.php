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
        Schema::table('sinh_vien', function (Blueprint $table) {
            $table->string('anh_sinh_vien')->default(null)->nullable()->after('ma_sinh_vien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sinh_vien', function (Blueprint $table) {
            $table->dropColumn('anh_sinh_vien');
        });
    }
};
