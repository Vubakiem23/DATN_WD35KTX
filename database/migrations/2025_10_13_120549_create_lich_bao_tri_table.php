<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lich_bao_tri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tai_san_id')->constrained('tai_san')->onDelete('cascade'); // liên kết tới tài sản
            $table->date('ngay_bao_tri'); // ngày bảo trì dự kiến
            $table->text('mo_ta')->nullable(); // mô tả
            $table->enum('trang_thai', ['Đang lên lịch', 'Đang bảo trì', 'Hoàn thành', 'Hoãn'])
                  ->default('Đang lên lịch');
            $table->date('ngay_hoan_thanh')->nullable(); // ngày hoàn thành (nếu có)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lich_bao_tri');
    }
};
