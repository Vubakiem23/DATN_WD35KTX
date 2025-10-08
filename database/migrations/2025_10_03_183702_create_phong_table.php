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
    Schema::create('phong', function (Blueprint $table) {
        $table->id();
        $table->string('ten_phong');
        $table->string('khu');
        $table->string('loai_phong');
        $table->integer('suc_chua');
        $table->string('trang_thai');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phong');
    }
};
