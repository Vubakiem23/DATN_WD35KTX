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
        $table->unsignedBigInteger('loai_id')->after('id'); // hoặc sau cột nào phù hợp
        $table->foreign('loai_id')->references('id')->on('loai_tai_san')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('kho_tai_san', function (Blueprint $table) {
        $table->dropForeign(['loai_id']);
        $table->dropColumn('loai_id');
    });
}

};
