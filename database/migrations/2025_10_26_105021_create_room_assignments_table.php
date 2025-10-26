<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('room_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sinh_vien_id');
            $table->unsignedBigInteger('phong_id');
            $table->date('start_date');
            $table->date('end_date')->nullable(); // null = đang ở
            $table->timestamps();

            $table->foreign('sinh_vien_id')->references('id')->on('sinh_vien')->onDelete('cascade');
            $table->foreign('phong_id')->references('id')->on('phong')->onDelete('cascade');

            $table->index(['sinh_vien_id', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_assignments');
    }
};
