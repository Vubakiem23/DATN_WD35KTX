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
        Schema::create('hoa_don', function (Blueprint $table) {
            $table->id();

            // Cột liên kết phòng
            $table->unsignedBigInteger('phong_id')->nullable();
            $table->foreign('phong_id')->references('id')->on('phong')->onDelete('cascade');

            // Các thông tin điện nước
            $table->integer('so_dien_cu')->nullable();
            $table->integer('so_dien_moi')->nullable();
            $table->integer('so_nuoc_cu')->nullable();
            $table->integer('so_nuoc_moi')->nullable();
            $table->decimal('don_gia_dien', 8, 2)->nullable();
            $table->decimal('don_gia_nuoc', 8, 2)->nullable();
            $table->decimal('thanh_tien', 10, 2)->nullable();
            $table->string('thang', 7)->nullable(); // ví dụ: "10/2025"

            // Cột sinh viên (cho phép null hoàn toàn)
            $table->unsignedBigInteger('sinh_vien_id')->nullable();

            // Các thông tin hóa đơn khác
            $table->string('loai_phi')->nullable();
            $table->decimal('so_tien', 10, 2)->nullable();
            $table->date('ngay_tao')->nullable();
            $table->string('trang_thai')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_don');
    }
};
