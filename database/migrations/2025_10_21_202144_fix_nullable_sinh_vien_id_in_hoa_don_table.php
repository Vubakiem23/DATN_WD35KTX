<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Xóa khóa ngoại nếu có
        try {
            DB::statement('ALTER TABLE hoa_don DROP FOREIGN KEY hoa_don_sinh_vien_id_foreign;');
        } catch (\Throwable $e) {
            // Bỏ qua nếu không tồn tại
        }

        // Sửa lại cột cho phép NULL
        DB::statement('ALTER TABLE hoa_don MODIFY sinh_vien_id BIGINT UNSIGNED NULL;');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE hoa_don MODIFY sinh_vien_id BIGINT UNSIGNED NOT NULL;');
    }
};
