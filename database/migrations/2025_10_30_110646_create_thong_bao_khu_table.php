<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_bao_khu', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại tới bảng thong_bao
            $table->unsignedBigInteger('thong_bao_id');
            $table->foreign('thong_bao_id')
                ->references('id')
                ->on('thong_bao')
                ->onDelete('cascade');

            // Khóa ngoại tới bảng khu
            $table->unsignedBigInteger('khu_id');
            $table->foreign('khu_id')
                ->references('id')
                ->on('khu')
                ->onDelete('cascade');

            // Đảm bảo một thông báo không bị trùng khu
            $table->unique(['thong_bao_id', 'khu_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_bao_khu');
    }
};
