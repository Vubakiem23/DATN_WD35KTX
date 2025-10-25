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
    Schema::table('kho_tai_san', function (Blueprint $table) {
        $table->string('tinh_trang')->nullable()->after('don_vi_tinh'); // hoặc vị trí khác
    });
}

public function down()
{
    Schema::table('tai_san', function (Blueprint $table) {
        $table->dropColumn('tinh_trang');
    });
}

};
