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
    Schema::create('su_co', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sinh_vien_id')->constrained('sinh_vien');
        $table->foreignId('phong_id')->constrained('phong');
        $table->text('mo_ta');
        $table->date('ngay_gui');
        $table->string('trang_thai');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('su_co');
    }
};
