<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phong', function (Blueprint $table) {
            if (!Schema::hasColumn('phong', 'khu_id')) {
                $table->unsignedBigInteger('khu_id')->nullable()->after('khu');
                $table->foreign('khu_id')->references('id')->on('khu')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('phong', function (Blueprint $table) {
            if (Schema::hasColumn('phong', 'khu_id')) {
                $table->dropForeign(['khu_id']);
                $table->dropColumn('khu_id');
            }
        });
    }
};



