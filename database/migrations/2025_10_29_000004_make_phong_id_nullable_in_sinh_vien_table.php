<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sinh_vien') && Schema::hasColumn('sinh_vien', 'phong_id')) {
            Schema::table('sinh_vien', function (Blueprint $table) {
                $table->unsignedBigInteger('phong_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sinh_vien') && Schema::hasColumn('sinh_vien', 'phong_id')) {
            Schema::table('sinh_vien', function (Blueprint $table) {
                $table->unsignedBigInteger('phong_id')->nullable(false)->change();
            });
        }
    }
};



