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
    Schema::create('chuyen_phong', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sinh_vien_id')->constrained('sinh_vien');
        $table->foreignId('phong_cu_id')->constrained('phong');
        $table->foreignId('phong_moi_id')->constrained('phong');
        $table->text('ly_do');
        $table->string('trang_thai');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuyen_phong');
    }
};
