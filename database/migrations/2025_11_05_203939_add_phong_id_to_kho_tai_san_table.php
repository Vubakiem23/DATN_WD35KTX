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
        $table->foreignId('phong_id')->nullable()->constrained('phong')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('kho_tai_san', function (Blueprint $table) {
        $table->dropForeign(['phong_id']);
        $table->dropColumn('phong_id');
    });
}

};
