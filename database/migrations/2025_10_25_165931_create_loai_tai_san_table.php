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
    Schema::create('loai_tai_san', function (Blueprint $table) {
        $table->id();
        $table->string('ma_loai')->unique(); // Mã loại tự động sinh
        $table->string('ten_loai');
        $table->text('mo_ta')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loai_tai_san');
    }
};
