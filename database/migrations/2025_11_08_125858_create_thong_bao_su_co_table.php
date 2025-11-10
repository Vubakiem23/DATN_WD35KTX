<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('thong_bao_su_co', function (Blueprint $table) {
            $table->id();
            $table->foreignId('su_co_id')->constrained('su_co')->onDelete('cascade');
            $table->string('tieu_de');
            $table->text('noi_dung');
            $table->timestamp('ngay_tao')->nullable();
            // $table->timestamps(); // nếu muốn created_at/updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_bao_su_co');
    }
};
