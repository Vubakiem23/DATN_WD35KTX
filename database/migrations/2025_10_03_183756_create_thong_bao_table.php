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
    Schema::create('thong_bao', function (Blueprint $table) {
        $table->id();
        $table->string('tieu_de');
        $table->text('noi_dung');
        $table->date('ngay_dang');
        $table->string('doi_tuong');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_bao');
    }
};
