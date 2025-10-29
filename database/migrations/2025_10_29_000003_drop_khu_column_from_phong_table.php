<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('phong') && Schema::hasColumn('phong', 'khu')) {
            Schema::table('phong', function (Blueprint $table) {
                $table->dropColumn('khu');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('phong') && !Schema::hasColumn('phong', 'khu')) {
            Schema::table('phong', function (Blueprint $table) {
                $table->string('khu', 100)->nullable();
            });
        }
    }
};



