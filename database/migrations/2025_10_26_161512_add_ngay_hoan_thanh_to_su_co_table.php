<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            $table->timestamp('ngay_hoan_thanh')->nullable()->after('trang_thai'); // 🆕 cột ngày hoàn thành
        });
    }

    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            $table->dropColumn('ngay_hoan_thanh');
        });
    }
};
