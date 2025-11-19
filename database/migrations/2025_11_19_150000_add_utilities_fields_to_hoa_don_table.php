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
        Schema::table('hoa_don', function (Blueprint $table) {
            $table->boolean('sent_dien_nuoc_to_client')
                ->default(false)
                ->after('sent_to_client_at');
            $table->timestamp('sent_dien_nuoc_at')
                ->nullable()
                ->after('sent_dien_nuoc_to_client');

            $table->boolean('da_thanh_toan_dien_nuoc')
                ->default(false)
                ->after('da_thanh_toan');
            $table->timestamp('ngay_thanh_toan_dien_nuoc')
                ->nullable()
                ->after('da_thanh_toan_dien_nuoc');
            $table->string('hinh_thuc_thanh_toan_dien_nuoc')
                ->nullable()
                ->after('ngay_thanh_toan_dien_nuoc');
            $table->text('ghi_chu_thanh_toan_dien_nuoc')
                ->nullable()
                ->after('hinh_thuc_thanh_toan_dien_nuoc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoa_don', function (Blueprint $table) {
            $table->dropColumn([
                'sent_dien_nuoc_to_client',
                'sent_dien_nuoc_at',
                'da_thanh_toan_dien_nuoc',
                'ngay_thanh_toan_dien_nuoc',
                'hinh_thuc_thanh_toan_dien_nuoc',
                'ghi_chu_thanh_toan_dien_nuoc',
            ]);
        });
    }
};

