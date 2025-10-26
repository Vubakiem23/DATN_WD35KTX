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
        Schema::create('thong_bao', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de');
            $table->text('noi_dung');
            $table->date('ngay_dang');
            $table->string('doi_tuong');
            $table->string('anh')->nullable()->comment('Đường dẫn ảnh');

            // Liên kết đến phòng, khi cần sẽ lấy mã phòng và khu từ bảng phong
            $table->foreignId('phong_id')
                  ->nullable()
                  ->constrained('phong')
                  ->onDelete('set null')
                  ->comment('Liên kết đến phòng, lấy mã phòng và khu từ bảng phong');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_bao');
    }
};
