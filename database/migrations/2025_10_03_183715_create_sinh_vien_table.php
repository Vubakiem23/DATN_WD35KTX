<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('sinh_vien', function (Blueprint $table) {
        $table->id();
        $table->string('ma_sinh_vien')->unique();
        $table->string('ho_ten');
        $table->date('ngay_sinh');
        $table->string('gioi_tinh');
        $table->string('que_quan');
        $table->string('noi_o_hien_tai');
        $table->string('lop');
        $table->string('nganh');
        $table->string('khoa_hoc');
        $table->string('so_dien_thoai');
        $table->string('email');
        
        $table->unsignedBigInteger('phong_id');
        $table->foreign('phong_id')->references('id')->on('phong')->onDelete('cascade');
        
        $table->string('trang_thai_ho_so');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinh_vien');
    }
};
