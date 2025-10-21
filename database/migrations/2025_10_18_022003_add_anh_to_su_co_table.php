<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            // Thêm cột ảnh minh chứng (tùy chọn có thể null)
            $table->string('anh')->nullable()->after('mo_ta');
        });
    }

    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            $table->dropColumn('anh');
        });
    }
};
