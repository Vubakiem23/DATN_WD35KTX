<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->string('hinh_anh_truoc', 255)->nullable()->after('hinh_anh');
        });
    }

    public function down(): void
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->dropColumn('hinh_anh_truoc');
        });
    }
};
