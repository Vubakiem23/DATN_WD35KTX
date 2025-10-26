<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->foreignId('kho_tai_san_id')->nullable()->constrained('kho_tai_san')->after('tai_san_id')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('lich_bao_tri', function (Blueprint $table) {
            $table->dropForeign(['kho_tai_san_id']);
            $table->dropColumn('kho_tai_san_id');
        });
    }
};

