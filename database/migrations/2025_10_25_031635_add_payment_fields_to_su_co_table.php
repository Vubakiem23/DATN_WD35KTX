<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            $table->decimal('payment_amount', 10, 2)->default(0)->after('trang_thai');
            $table->boolean('is_paid')->default(false)->after('payment_amount'); // false = unpaid, true = paid
            $table->enum('nguoi_tao', ['sinh_vien', 'nhan_vien'])->default('sinh_vien')->after('is_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            $table->dropColumn(['payment_amount', 'is_paid', 'nguoi_tao']);
        });
    }
};
