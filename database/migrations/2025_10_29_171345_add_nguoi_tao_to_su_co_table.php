<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            if (!Schema::hasColumn('su_co', 'nguoi_tao')) {
                $table->string('nguoi_tao', 50)->nullable()->after('is_paid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            if (Schema::hasColumn('su_co', 'nguoi_tao')) {
                $table->dropColumn('nguoi_tao');
            }
        });
    }
};
