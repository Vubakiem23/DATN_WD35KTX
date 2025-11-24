<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->enum('trang_thai', [
                'Đang lên lịch',
                'Chờ bảo trì',
                'Đang bảo trì',
                'Hoàn thành',
                'Từ chối tiếp nhận'
            ])->default('Đang lên lịch')->change();
        });
    }

    public function down(): void
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->enum('trang_thai', [
                'Đang lên lịch',
                'Chờ bảo trì',
                'Đang bảo trì',
                'Hoàn thành'
            ])->default('Đang lên lịch')->change();
        });
    }
};

