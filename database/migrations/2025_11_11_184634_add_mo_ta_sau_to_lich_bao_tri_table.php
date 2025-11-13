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
    Schema::table('lich_bao_tri', function (Blueprint $table) {
        $table->text('mo_ta_sau')->nullable()->after('mo_ta');
    });
}

public function down()
{
    Schema::table('lich_bao_tri', function (Blueprint $table) {
        $table->dropColumn('mo_ta_sau');
    });
}

};
