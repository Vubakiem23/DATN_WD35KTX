<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('khu')) {
            return;
        }

        Schema::table('khu', function (Blueprint $table) {
            if (!Schema::hasColumn('khu', 'gia_moi_slot')) {
                $table->unsignedBigInteger('gia_moi_slot')
                    ->default(0)
                    ->after('mo_ta')
                    ->comment('Đơn giá mỗi slot (mỗi sinh viên) theo tháng, đơn vị VND');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('khu')) {
            return;
        }

        Schema::table('khu', function (Blueprint $table) {
            if (Schema::hasColumn('khu', 'gia_moi_slot')) {
                $table->dropColumn('gia_moi_slot');
            }
        });
    }
};



