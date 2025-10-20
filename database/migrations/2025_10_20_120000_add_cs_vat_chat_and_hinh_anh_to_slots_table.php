<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('slots')) return;
        Schema::table('slots', function (Blueprint $table) {
            if (!Schema::hasColumn('slots', 'cs_vat_chat')) {
                $table->text('cs_vat_chat')->nullable()->after('ghi_chu');
            }
            if (!Schema::hasColumn('slots', 'hinh_anh')) {
                $table->string('hinh_anh')->nullable()->after('cs_vat_chat');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('slots')) return;
        Schema::table('slots', function (Blueprint $table) {
            if (Schema::hasColumn('slots', 'hinh_anh')) {
                $table->dropColumn('hinh_anh');
            }
            if (Schema::hasColumn('slots', 'cs_vat_chat')) {
                $table->dropColumn('cs_vat_chat');
            }
        });
    }
};



