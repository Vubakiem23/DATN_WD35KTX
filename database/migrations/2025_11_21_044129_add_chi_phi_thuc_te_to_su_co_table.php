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
            if (!Schema::hasColumn('su_co', 'chi_phi_thuc_te')) {
                $table->decimal('chi_phi_thuc_te', 15, 2)->nullable()->after('payment_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            $table->dropColumn('chi_phi_thuc_te');
        });
    }
};
