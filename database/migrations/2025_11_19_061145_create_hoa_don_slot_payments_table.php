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
        Schema::create('hoa_don_slot_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hoa_don_id');
            $table->unsignedBigInteger('slot_id')->nullable(); // ID của slot
            $table->string('slot_label'); // Tên slot (vd: 101-01, 101-S2)
            $table->unsignedBigInteger('sinh_vien_id')->nullable(); // ID sinh viên
            $table->string('sinh_vien_ten')->nullable(); // Tên sinh viên
            $table->boolean('da_thanh_toan')->default(false);
            $table->timestamp('ngay_thanh_toan')->nullable();
            $table->string('hinh_thuc_thanh_toan')->nullable(); // tien_mat, chuyen_khoan
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->foreign('hoa_don_id')->references('id')->on('hoa_don')->onDelete('cascade');
            $table->foreign('slot_id')->references('id')->on('slots')->onDelete('set null');
            $table->foreign('sinh_vien_id')->references('id')->on('sinh_vien')->onDelete('set null');
            
            $table->index(['hoa_don_id', 'slot_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_don_slot_payments');
    }
};
