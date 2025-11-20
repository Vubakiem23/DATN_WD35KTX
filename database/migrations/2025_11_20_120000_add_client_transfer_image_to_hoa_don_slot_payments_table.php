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
        Schema::table('hoa_don_slot_payments', function (Blueprint $table) {
            $table->string('client_transfer_image_path')
                ->nullable()
                ->after('client_ghi_chu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoa_don_slot_payments', function (Blueprint $table) {
            $table->dropColumn('client_transfer_image_path');
        });
    }
};

