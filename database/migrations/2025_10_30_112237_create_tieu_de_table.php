<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tieu_de', function (Blueprint $table) {
            $table->id();
            $table->string('ten_tieu_de')->unique(); // Tiêu đề riêng
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tieu_de');
    }
};
