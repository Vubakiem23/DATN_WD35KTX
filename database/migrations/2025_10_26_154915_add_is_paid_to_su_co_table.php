<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('payment_amount');
        });
    }

    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            $table->dropColumn('is_paid');
        });
    }
};
