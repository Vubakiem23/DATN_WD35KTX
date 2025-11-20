<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoa_don_utilities_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoa_don_id')->constrained('hoa_don')->cascadeOnDelete();
            $table->foreignId('slot_id')->nullable()->constrained('slots')->nullOnDelete();
            $table->string('slot_label')->nullable();
            $table->foreignId('sinh_vien_id')->nullable()->constrained('sinh_vien')->nullOnDelete();
            $table->string('sinh_vien_ten')->nullable();
            $table->unsignedBigInteger('tien_dien')->default(0);
            $table->unsignedBigInteger('tien_nuoc')->default(0);
            $table->unsignedBigInteger('tong_tien')->default(0);
            $table->string('trang_thai')->default('chua_thanh_toan');
            $table->boolean('da_thanh_toan')->default(false);
            $table->timestamp('ngay_thanh_toan')->nullable();
            $table->string('hinh_thuc_thanh_toan')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->text('client_ghi_chu')->nullable();
            $table->string('client_transfer_image_path')->nullable();
            $table->timestamp('client_requested_at')->nullable();
            $table->foreignId('xac_nhan_boi')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['hoa_don_id', 'slot_label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoa_don_utilities_payments');
    }
};

