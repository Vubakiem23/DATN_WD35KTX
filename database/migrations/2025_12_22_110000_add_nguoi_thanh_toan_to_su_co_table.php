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
            $table->string('nguoi_thanh_toan')->nullable()->after('is_paid');
            // 'ktx' = KTX thanh toán (chi phí)
            // 'client' = Sinh viên thanh toán (thu nhập)
            // null = Chưa thanh toán hoặc không có thanh toán
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            $table->dropColumn('nguoi_thanh_toan');
        });
    }
};
