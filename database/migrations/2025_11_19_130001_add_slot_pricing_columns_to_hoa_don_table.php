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
        Schema::table('hoa_don', function (Blueprint $table) {
            if (!Schema::hasColumn('hoa_don', 'tien_phong_slot')) {
                $table->unsignedBigInteger('tien_phong_slot')->default(0)->after('thanh_tien');
            }

            if (!Schema::hasColumn('hoa_don', 'slot_unit_price')) {
                $table->unsignedBigInteger('slot_unit_price')->default(0)->after('tien_phong_slot');
            }

            if (!Schema::hasColumn('hoa_don', 'slot_billing_count')) {
                $table->unsignedInteger('slot_billing_count')->nullable()->after('slot_unit_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoa_don', function (Blueprint $table) {
            if (Schema::hasColumn('hoa_don', 'slot_billing_count')) {
                $table->dropColumn('slot_billing_count');
            }

            if (Schema::hasColumn('hoa_don', 'slot_unit_price')) {
                $table->dropColumn('slot_unit_price');
            }

            if (Schema::hasColumn('hoa_don', 'tien_phong_slot')) {
                $table->dropColumn('tien_phong_slot');
            }
        });
    }
};


