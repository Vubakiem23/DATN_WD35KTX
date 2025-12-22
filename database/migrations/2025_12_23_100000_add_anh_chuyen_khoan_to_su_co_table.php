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
            if (!Schema::hasColumn('su_co', 'anh_chuyen_khoan')) {
                $table->string('anh_chuyen_khoan')->nullable()->after('payment_note');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            if (Schema::hasColumn('su_co', 'anh_chuyen_khoan')) {
                $table->dropColumn('anh_chuyen_khoan');
            }
        });
    }
};
