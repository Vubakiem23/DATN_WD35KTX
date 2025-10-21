<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlotsTable extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('phong_id');
            $table->string('ma_slot'); // VD: P101-S1
            $table->unsignedBigInteger('sinh_vien_id')->nullable(); // nếu slot trống thì null
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            // Ràng buộc khóa ngoại
            $table->foreign('phong_id')->references('id')->on('phong')->onDelete('cascade');
            $table->foreign('sinh_vien_id')->references('id')->on('sinh_vien')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
}
