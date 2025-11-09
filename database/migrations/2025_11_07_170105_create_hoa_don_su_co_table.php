<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hoa_don_su_co', function (Blueprint $table) {
            $table->id();

            // 🔗 Liên kết khóa ngoại
            $table->unsignedBigInteger('su_co_id');
            $table->unsignedBigInteger('sinh_vien_id')->nullable();
            $table->unsignedBigInteger('phong_id')->nullable();

            // 💰 Thông tin hóa đơn
            $table->decimal('amount', 12, 0)->default(0);
            $table->string('status')->default('Chưa thanh toán'); // hoặc: Đã thanh toán
            $table->date('ngay_tao')->nullable(); // ✅ sửa dòng này
            $table->date('ngay_thanh_toan')->nullable();

            $table->timestamps();

            // 🔐 Khóa ngoại
            $table->foreign('su_co_id')->references('id')->on('su_co')->onDelete('cascade');
            $table->foreign('sinh_vien_id')->references('id')->on('sinh_vien')->onDelete('set null');
            $table->foreign('phong_id')->references('id')->on('phong')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoa_don_su_co');
    }
};
