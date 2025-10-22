<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slot_tai_san', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slot_id');
            $table->unsignedBigInteger('tai_san_id');
            $table->unsignedInteger('so_luong')->default(1);
            $table->timestamps();

            $table->foreign('slot_id')->references('id')->on('slots')->onDelete('cascade');
            $table->foreign('tai_san_id')->references('id')->on('tai_san')->onDelete('cascade');
            $table->unique(['slot_id', 'tai_san_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slot_tai_san');
    }
};


