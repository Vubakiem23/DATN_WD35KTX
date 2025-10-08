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
    Schema::create('dang_ky_k_t_x', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sinh_vien_id')->constrained('sinh_vien');
        $table->date('ngay_dang_ky');
        $table->string('trang_thai');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dang_ky_k_t_x');
    }
};
