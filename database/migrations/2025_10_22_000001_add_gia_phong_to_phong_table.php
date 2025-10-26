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
        if (!Schema::hasTable('phong')) return;
        Schema::table('phong', function (Blueprint $table) {
            if (!Schema::hasColumn('phong', 'gia_phong')) {
                $table->unsignedBigInteger('gia_phong')
                      ->after('suc_chua')
                      ->comment('Đơn giá theo tháng (VND)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('phong')) return;
        Schema::table('phong', function (Blueprint $table) {
            if (Schema::hasColumn('phong', 'gia_phong')) {
                $table->dropColumn('gia_phong');
            }
        });
    }
};



