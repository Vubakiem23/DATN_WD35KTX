<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->unsignedBigInteger('tai_san_id')->nullable()->change();
            $table->unsignedBigInteger('kho_tai_san_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->unsignedBigInteger('tai_san_id')->nullable(false)->change();
            $table->unsignedBigInteger('kho_tai_san_id')->nullable(false)->change();
        });
    }
};
