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
        if (!Schema::hasTable('phong')) return;
        Schema::table('phong', function (Blueprint $table) {
            if (!Schema::hasColumn('phong', 'hinh_anh')) {
                $table->string('hinh_anh')->nullable()->after('trang_thai');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('phong')) return;
        Schema::table('phong', function (Blueprint $table) {
            if (Schema::hasColumn('phong', 'hinh_anh')) {
                $table->dropColumn('hinh_anh');
            }
        });
    }
};
