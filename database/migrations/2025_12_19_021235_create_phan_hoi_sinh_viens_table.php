<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phan_hoi_sinh_viens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sinh_vien_id');
            $table->string('tieu_de')->nullable();
            $table->text('noi_dung');
            $table->tinyInteger('trang_thai')
                ->default(0)
                ->comment('1: đã xử lý, 0: chờ xử lý');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phan_hoi_sinh_viens');
    }
};
