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
    Schema::create('hoa_don', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sinh_vien_id')->constrained('sinh_vien');
        $table->string('loai_phi');
        $table->decimal('so_tien');
        $table->date('ngay_tao');
        $table->string('trang_thai');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_don');
    }
};
