<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('su_co', function (Blueprint $table) {
        if (!Schema::hasColumn('su_co', 'payment_method')) {
            $table->string('payment_method')->nullable()->after('trang_thai');
        }
        if (!Schema::hasColumn('su_co', 'payment_note')) {
            $table->string('payment_note')->nullable()->after('payment_method');
        }
        if (!Schema::hasColumn('su_co', 'ngay_thanh_toan')) {
            $table->timestamp('ngay_thanh_toan')->nullable()->after('is_paid');
        }
    });
}

public function down()
{
    Schema::table('su_co', function (Blueprint $table) {
        $table->dropColumn(['payment_method', 'payment_note', 'ngay_thanh_toan']);
    });
}

};
