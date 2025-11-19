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
            $table->string('trang_thai')->default('chua_thanh_toan')->after('sinh_vien_ten');
            $table->timestamp('client_requested_at')->nullable()->after('trang_thai');
            $table->unsignedBigInteger('xac_nhan_boi')->nullable()->after('ngay_thanh_toan');
            $table->text('client_ghi_chu')->nullable()->after('ghi_chu');

            $table->foreign('xac_nhan_boi')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoa_don_slot_payments', function (Blueprint $table) {
            $table->dropForeign(['xac_nhan_boi']);
            $table->dropColumn([
                'trang_thai',
                'client_requested_at',
                'xac_nhan_boi',
                'client_ghi_chu',
            ]);
        });
    }
};

