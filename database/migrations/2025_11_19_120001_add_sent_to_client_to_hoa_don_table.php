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
        Schema::table('hoa_don', function (Blueprint $table) {
            if (!Schema::hasColumn('hoa_don', 'sent_to_client')) {
                $table->boolean('sent_to_client')->default(false)->after('da_thanh_toan');
            }

            if (!Schema::hasColumn('hoa_don', 'sent_to_client_at')) {
                $table->timestamp('sent_to_client_at')->nullable()->after('sent_to_client');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoa_don', function (Blueprint $table) {
            if (Schema::hasColumn('hoa_don', 'sent_to_client_at')) {
                $table->dropColumn('sent_to_client_at');
            }

            if (Schema::hasColumn('hoa_don', 'sent_to_client')) {
                $table->dropColumn('sent_to_client');
            }
        });
    }
};

