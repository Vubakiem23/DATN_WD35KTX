<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            if (!Schema::hasColumn('su_co', 'is_paid')) {
                // Không phụ thuộc cột trước đó để tránh lỗi môi trường khác nhau
                $table->boolean('is_paid')->default(false);
            }
        });
    }


    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            if (Schema::hasColumn('su_co', 'is_paid')) {
                $table->dropColumn('is_paid');
            }
        });
    }
};
