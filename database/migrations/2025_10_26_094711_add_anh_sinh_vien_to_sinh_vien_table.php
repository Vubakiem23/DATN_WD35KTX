<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Chỉ thêm cột nếu chưa có
        if (!Schema::hasColumn('sinh_vien', 'anh_sinh_vien')) {
            Schema::table('sinh_vien', function (Blueprint $table) {
                $table->string('anh_sinh_vien')->nullable()->after('email');
            });
        }
    }

    public function down(): void
    {
        // Chỉ drop nếu đang tồn tại
        if (Schema::hasColumn('sinh_vien', 'anh_sinh_vien')) {
            Schema::table('sinh_vien', function (Blueprint $table) {
                $table->dropColumn('anh_sinh_vien');
            });
        }
    }
};
