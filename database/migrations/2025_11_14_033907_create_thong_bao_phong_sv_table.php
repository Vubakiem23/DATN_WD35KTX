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
    Schema::create('thong_bao_phong_sv', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('sinh_vien_id');
        $table->unsignedBigInteger('phong_id');

        // Nội dung thông báo
        $table->string('noi_dung');

        $table->timestamps();

        $table->foreign('sinh_vien_id')
              ->references('id')->on('sinh_vien')
              ->onDelete('cascade');

        $table->foreign('phong_id')
              ->references('id')->on('phong')
              ->onDelete('cascade');
    });
}

};
