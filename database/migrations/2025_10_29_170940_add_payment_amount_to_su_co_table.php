<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            if (!Schema::hasColumn('su_co', 'payment_amount')) {
                $table->decimal('payment_amount', 10, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            if (Schema::hasColumn('su_co', 'payment_amount')) {
                $table->dropColumn('payment_amount');
            }
        });
    }
};
