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
    Schema::create('tai_san', function (Blueprint $table) {
        $table->id();
        $table->string('ten_tai_san');
        $table->foreignId('phong_id')->constrained('phong');
        $table->integer('so_luong');
        $table->string('tinh_trang');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tai_san');
    }
};
