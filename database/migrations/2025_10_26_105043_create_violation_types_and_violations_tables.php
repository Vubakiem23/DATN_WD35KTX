<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('violation_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();   // ví dụ LATE_RENT
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sinh_vien_id');
            $table->unsignedBigInteger('violation_type_id');
            $table->dateTime('occurred_at')->index();
            $table->string('status')->default('open'); // open, resolved
            $table->decimal('penalty_amount', 12, 2)->nullable();
            $table->string('receipt_no')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('sinh_vien_id')->references('id')->on('sinh_vien')->onDelete('cascade');
            $table->foreign('violation_type_id')->references('id')->on('violation_types')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violations');
        Schema::dropIfExists('violation_types');
    }
};
