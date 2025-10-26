<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing FKs
        Schema::table('chuyen_phong', function (Blueprint $table) {
            try { $table->dropForeign(['phong_cu_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['phong_moi_id']); } catch (\Throwable $e) {}
        });

        // Make columns nullable
        DB::statement('ALTER TABLE `chuyen_phong` MODIFY `phong_cu_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `chuyen_phong` MODIFY `phong_moi_id` BIGINT UNSIGNED NULL');

        // Recreate FKs with SET NULL on delete
        Schema::table('chuyen_phong', function (Blueprint $table) {
            $table->foreign('phong_cu_id')->references('id')->on('phong')->onDelete('set null');
            $table->foreign('phong_moi_id')->references('id')->on('phong')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('chuyen_phong', function (Blueprint $table) {
            try { $table->dropForeign(['phong_cu_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['phong_moi_id']); } catch (\Throwable $e) {}
        });
        try { DB::statement('ALTER TABLE `chuyen_phong` MODIFY `phong_cu_id` BIGINT UNSIGNED NOT NULL'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE `chuyen_phong` MODIFY `phong_moi_id` BIGINT UNSIGNED NOT NULL'); } catch (\Throwable $e) {}
        Schema::table('chuyen_phong', function (Blueprint $table) {
            $table->foreign('phong_cu_id')->references('id')->on('phong');
            $table->foreign('phong_moi_id')->references('id')->on('phong');
        });
    }
};



