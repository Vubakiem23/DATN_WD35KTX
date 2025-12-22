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
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->string('nguoi_tao')->default('admin')->after('trang_thai');
            // 'admin' = Admin tạo lịch bảo trì
            // 'client' = Sinh viên báo hỏng
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->dropColumn('nguoi_tao');
        });
    }
};
