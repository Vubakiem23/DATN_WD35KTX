<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_bao_phong', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại tới bảng thong_bao
            $table->unsignedBigInteger('thong_bao_id');
            $table->foreign('thong_bao_id')
                ->references('id')
                ->on('thong_bao')
                ->onDelete('cascade');

            // Khóa ngoại tới bảng phong
            $table->unsignedBigInteger('phong_id');
            $table->foreign('phong_id')
                ->references('id')
                ->on('phong')
                ->onDelete('cascade');

            // Tránh trùng thông báo-phòng
            $table->unique(['thong_bao_id', 'phong_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_bao_phong');
    }
};
