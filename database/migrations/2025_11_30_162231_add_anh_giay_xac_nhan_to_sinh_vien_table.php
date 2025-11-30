<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Chỉ thêm cột nếu chưa có
        if (!Schema::hasColumn('sinh_vien', 'anh_giay_xac_nhan')) {
        Schema::table('sinh_vien', function (Blueprint $table) {
                $table->string('anh_giay_xac_nhan')->nullable()->after('anh_sinh_vien');
        });
        }
    }

    public function down(): void
    {
        // Chỉ drop nếu đang tồn tại
        if (Schema::hasColumn('sinh_vien', 'anh_giay_xac_nhan')) {
        Schema::table('sinh_vien', function (Blueprint $table) {
                $table->dropColumn('anh_giay_xac_nhan');
        });
        }
    }
};
