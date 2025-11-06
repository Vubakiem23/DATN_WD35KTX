<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
        ALTER TABLE lich_bao_tri 
        MODIFY COLUMN trang_thai 
        ENUM('Đang lên lịch', 'Chờ bảo trì', 'Đang bảo trì', 'Hoàn thành', 'Hoãn') 
        DEFAULT 'Đang lên lịch'
    ");
    }

    public function down(): void
    {
        DB::statement("
        ALTER TABLE lich_bao_tri 
        MODIFY COLUMN trang_thai 
        ENUM('Đang lên lịch', 'Đang bảo trì', 'Hoàn thành', 'Hoãn') 
        DEFAULT 'Đang lên lịch'
    ");
    }
};
