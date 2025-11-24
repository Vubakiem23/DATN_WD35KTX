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
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            if (!Schema::hasColumn('lich_bao_tri', 'chi_phi')) {
                $table->decimal('chi_phi', 15, 2)->nullable()->after('ngay_hoan_thanh');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->dropColumn('chi_phi');
        });
    }
};
