<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hoa_don', function (Blueprint $table) {
            if (!Schema::hasColumn('hoa_don', 'so_dien_cu')) {
                $table->integer('so_dien_cu')->nullable();
            }
            if (!Schema::hasColumn('hoa_don', 'so_dien_moi')) {
                $table->integer('so_dien_moi')->nullable();
            }
            if (!Schema::hasColumn('hoa_don', 'so_nuoc_cu')) {
                $table->integer('so_nuoc_cu')->nullable();
            }
            if (!Schema::hasColumn('hoa_don', 'so_nuoc_moi')) {
                $table->integer('so_nuoc_moi')->nullable();
            }
            if (!Schema::hasColumn('hoa_don', 'don_gia_dien')) {
                $table->decimal('don_gia_dien', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('hoa_don', 'don_gia_nuoc')) {
                $table->decimal('don_gia_nuoc', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('hoa_don', 'thanh_tien')) {
                $table->decimal('thanh_tien', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('hoa_don', 'thang')) {
                $table->string('thang', 7)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('hoa_don', function (Blueprint $table) {
            $table->dropColumn([
                'so_dien_cu', 'so_dien_moi',
                'so_nuoc_cu', 'so_nuoc_moi',
                'don_gia_dien', 'don_gia_nuoc',
                'thanh_tien', 'thang'
            ]);
        });
    }
};
