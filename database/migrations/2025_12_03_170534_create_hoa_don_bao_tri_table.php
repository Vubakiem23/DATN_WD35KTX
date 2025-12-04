<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoa_don_bao_tri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lich_bao_tri_id'); // liên kết với lịch bảo trì
            $table->decimal('chi_phi', 15, 2);
            $table->string('trang_thai_thanh_toan')->default('Chưa thanh toán'); // Chưa thanh toán / Đã thanh toán
            $table->string('phuong_thuc_thanh_toan')->nullable(); // Tiền mặt / Chuyển khoản / vv
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->foreign('lich_bao_tri_id')->references('id')->on('lich_bao_tri')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoa_don_bao_tri');
    }
};
