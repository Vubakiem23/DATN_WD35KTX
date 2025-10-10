<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGioiTinhToPhong extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('phong')) {
            return;
        }

        Schema::table('phong', function (Blueprint $table) {
            if (!Schema::hasColumn('phong', 'gioi_tinh')) {
                $table->string('gioi_tinh', 50)
                      ->nullable()
                      ->after('loai_phong')
                      ->comment('Nam|Nữ|Cả hai (quy định của phòng)');
            }
            if (!Schema::hasColumn('phong', 'ghi_chu')) {
                $table->string('ghi_chu', 255)
                      ->nullable()
                      ->after('trang_thai')
                      ->comment('Ghi chú về phòng');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('phong')) return;

        Schema::table('phong', function (Blueprint $table) {
            if (Schema::hasColumn('phong', 'gioi_tinh')) {
                $table->dropColumn('gioi_tinh');
            }
            if (Schema::hasColumn('phong', 'ghi_chu')) {
                $table->dropColumn('ghi_chu');
            }
        });
    }
}
