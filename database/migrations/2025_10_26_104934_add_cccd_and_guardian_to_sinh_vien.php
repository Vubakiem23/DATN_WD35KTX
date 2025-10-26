<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sinh_vien', function (Blueprint $table) {
            // CCCD
            if (!Schema::hasColumn('sinh_vien', 'citizen_id_number')) {
                $table->string('citizen_id_number')->nullable()->after('email');
            }
            if (!Schema::hasColumn('sinh_vien', 'citizen_issue_date')) {
                $table->date('citizen_issue_date')->nullable()->after('citizen_id_number');
            }
            if (!Schema::hasColumn('sinh_vien', 'citizen_issue_place')) {
                $table->string('citizen_issue_place')->nullable()->after('citizen_issue_date');
            }

            // Người thân
            if (!Schema::hasColumn('sinh_vien', 'guardian_name')) {
                $table->string('guardian_name')->nullable()->after('citizen_issue_place');
            }
            if (!Schema::hasColumn('sinh_vien', 'guardian_phone')) {
                $table->string('guardian_phone')->nullable()->after('guardian_name');
            }
            if (!Schema::hasColumn('sinh_vien', 'guardian_relationship')) {
                $table->string('guardian_relationship')->nullable()->after('guardian_phone');
            }

            // Index giúp tìm kiếm nhanh
            $table->index(['citizen_id_number']);
            $table->index(['guardian_phone']);
        });
    }

    public function down(): void
    {
        Schema::table('sinh_vien', function (Blueprint $table) {
            $table->dropIndex(['citizen_id_number']);
            $table->dropIndex(['guardian_phone']);
            $table->dropColumn([
                'citizen_id_number',
                'citizen_issue_date',
                'citizen_issue_place',
                'guardian_name',
                'guardian_phone',
                'guardian_relationship',
            ]);
        });
    }
};
