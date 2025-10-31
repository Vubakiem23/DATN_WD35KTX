<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thong_bao', function (Blueprint $table) {

            // Thêm cột liên kết tiêu đề nếu chưa có
            if (!Schema::hasColumn('thong_bao', 'tieu_de_id')) {
                $table->foreignId('tieu_de_id')
                      ->nullable()
                      ->constrained('tieu_de')
                      ->onDelete('cascade')
                      ->after('id')
                      ->comment('Liên kết tới bảng tiêu đề');
            }

            // Thêm cột liên kết mức độ nếu chưa có
            if (!Schema::hasColumn('thong_bao', 'muc_do_id')) {
                $table->foreignId('muc_do_id')
                      ->nullable()
                      ->constrained('muc_do')
                      ->onDelete('set null')
                      ->after('tieu_de_id')
                      ->comment('Liên kết tới bảng mức độ');
            }

            // Thêm cột file upload nếu chưa có
            if (!Schema::hasColumn('thong_bao', 'file')) {
                $table->string('file')
                      ->nullable()
                      ->after('anh')
                      ->comment('Đường dẫn file PDF, Word, Excel');
            }
        });
    }

    public function down(): void
    {
        Schema::table('thong_bao', function (Blueprint $table) {
            if (Schema::hasColumn('thong_bao', 'tieu_de_id')) {
                $table->dropForeign(['tieu_de_id']);
                $table->dropColumn('tieu_de_id');
            }

            if (Schema::hasColumn('thong_bao', 'muc_do_id')) {
                $table->dropForeign(['muc_do_id']);
                $table->dropColumn('muc_do_id');
            }

            if (Schema::hasColumn('thong_bao', 'file')) {
                $table->dropColumn('file');
            }
        });
    }
};
