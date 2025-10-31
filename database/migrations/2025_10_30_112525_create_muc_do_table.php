<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('muc_do', function (Blueprint $table) {
            $table->id();
            $table->string('ten_muc_do')->unique()->comment('Tên mức độ, ví dụ: normal, important, urgent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muc_do');
    }
};
