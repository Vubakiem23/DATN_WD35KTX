<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    DB::statement("ALTER TABLE lich_bao_tri MODIFY trang_thai ENUM(
        'Đang lên lịch',
        'Chờ bảo trì',
        'Đang bảo trì',
        'Chờ thanh toán',
        'Hoàn thành',
        'Đã thanh toán',
        'Từ chối tiếp nhận'
    ) DEFAULT 'Đang lên lịch'");
}

public function down()
{
    DB::statement("ALTER TABLE lich_bao_tri MODIFY trang_thai ENUM(
        'Đang lên lịch',
        'Chờ bảo trì',
        'Đang bảo trì',
        'Hoàn thành',
        'Từ chối tiếp nhận'
    ) DEFAULT 'Đang lên lịch'");
}

};
