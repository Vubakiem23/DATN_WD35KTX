<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('violations', function (Blueprint $table) {
            $table->string('client_payment_method')
                ->nullable()
                ->after('note');
            $table->text('client_payment_note')
                ->nullable()
                ->after('client_payment_method');
            $table->string('client_transfer_image_path')
                ->nullable()
                ->after('client_payment_note');
            $table->timestamp('client_paid_at')
                ->nullable()
                ->after('client_transfer_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('violations', function (Blueprint $table) {
            $table->dropColumn([
                'client_payment_method',
                'client_payment_note',
                'client_transfer_image_path',
                'client_paid_at',
            ]);
        });
    }
};

