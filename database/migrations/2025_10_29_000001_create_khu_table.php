<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('khu', function (Blueprint $table) {
            $table->id();
            $table->string('ten_khu')->unique();
            $table->enum('gioi_tinh', ['Nam', 'Nữ']);
            $table->string('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('khu');
    }
};



