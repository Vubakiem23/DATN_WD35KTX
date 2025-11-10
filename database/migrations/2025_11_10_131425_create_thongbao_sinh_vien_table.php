<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('thongbao_sinh_vien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('sinh_vien')->onDelete('cascade');
            $table->string('noi_dung');           // Nội dung thông báo
            $table->string('trang_thai')->default('Chờ duyệt'); // trạng thái: Chờ duyệt / Đã duyệt
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thongbao_sinh_vien');
    }
};
