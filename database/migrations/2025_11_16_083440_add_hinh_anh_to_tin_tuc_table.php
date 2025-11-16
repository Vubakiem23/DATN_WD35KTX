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
        Schema::table('tin_tuc', function (Blueprint $table) {
    $table->string('hinh_anh')->nullable()->after('noi_dung');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tin_tuc', function (Blueprint $table) {
            $table->dropColumn('hinh_anh');
        });
    }
};
