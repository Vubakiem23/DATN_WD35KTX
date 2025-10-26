<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            // Drop existing FK then recreate with cascade on delete
            try {
                $table->dropForeign(['phong_id']);
            } catch (\Throwable $e) {
                // FK might already be dropped; ignore
            }
            $table->foreign('phong_id')
                ->references('id')
                ->on('phong')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            try {
                $table->dropForeign(['phong_id']);
            } catch (\Throwable $e) {
                // ignore
            }
            // Restore FK without cascade (original behavior)
            $table->foreign('phong_id')
                ->references('id')
                ->on('phong');
        });
    }
};



