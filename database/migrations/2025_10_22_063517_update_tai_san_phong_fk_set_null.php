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
        // 1) Drop existing foreign key on phong_id (name is auto-generated)
        Schema::table('tai_san', function (Blueprint $table) {
            try {
                $table->dropForeign(['phong_id']);
            } catch (\Throwable $e) {
                // Foreign key may not exist yet in some environments
            }
        });

        // 2) Make phong_id nullable without requiring doctrine/dbal
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `tai_san` MODIFY `phong_id` BIGINT UNSIGNED NULL');

        // 3) Recreate foreign key with ON DELETE SET NULL
        Schema::table('tai_san', function (Blueprint $table) {
            $table->foreign('phong_id')
                ->references('id')
                ->on('phong')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Best-effort revert: drop FK with SET NULL and restore FK without delete rule
        Schema::table('tai_san', function (Blueprint $table) {
            try {
                $table->dropForeign(['phong_id']);
            } catch (\Throwable $e) {
                // ignore if already dropped
            }
        });

        // Attempt to make column NOT NULL again (will fail if there are NULLs)
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `tai_san` MODIFY `phong_id` BIGINT UNSIGNED NOT NULL');
        } catch (\Throwable $e) {
            // If some rows are NULL this will be skipped to avoid breaking rollback
        }

        Schema::table('tai_san', function (Blueprint $table) {
            $table->foreign('phong_id')
                ->references('id')
                ->on('phong');
        });
    }
};
