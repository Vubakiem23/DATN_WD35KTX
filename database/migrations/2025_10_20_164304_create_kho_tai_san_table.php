<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kho_tai_san', function (Blueprint $table) {
            $table->id();
            $table->string('ma_tai_san')->unique()->comment('Mã định danh tài sản trong kho');
            $table->string('ten_tai_san')->comment('Tên tài sản');
            $table->string('don_vi_tinh')->nullable()->comment('Đơn vị tính, ví dụ: cái, bộ...');
            $table->integer('so_luong')->default(0)->comment('Số lượng trong kho');
            $table->string('hinh_anh')->nullable()->comment('Ảnh minh họa tài sản');
            $table->text('ghi_chu')->nullable()->comment('Ghi chú thêm');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kho_tai_san');
    }
};
