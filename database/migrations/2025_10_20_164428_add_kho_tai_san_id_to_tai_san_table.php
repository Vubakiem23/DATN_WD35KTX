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
    Schema::table('tai_san', function (Blueprint $table) {
        $table->unsignedBigInteger('kho_tai_san_id')->nullable()->after('id');
        $table->foreign('kho_tai_san_id')->references('id')->on('kho_tai_san')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('tai_san', function (Blueprint $table) {
        $table->dropForeign(['kho_tai_san_id']);
        $table->dropColumn('kho_tai_san_id');
    });
}

};
