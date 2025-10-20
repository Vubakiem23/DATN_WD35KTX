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
    Schema::table('tai_san', function (Blueprint $table) {
        $table->string('hinh_anh')->nullable()->after('ten_tai_san');
    });
}

public function down()
{
    Schema::table('tai_san', function (Blueprint $table) {
        $table->dropColumn('hinh_anh');
    });
}

};
