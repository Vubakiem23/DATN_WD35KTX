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
   $table->string('tinh_trang_hien_tai')->nullable()->after('tinh_trang');        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tai_san', function (Blueprint $table) {
        $table->dropColumn('tinh_trang_hien_tai');
        });
    }
};
