<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            // Độ hoàn thiện xử lý (0-100)
            $table->unsignedTinyInteger('completion_percent')->default(0)->after('trang_thai');
            // Ảnh sau khi xử lý (khác với ảnh ban đầu khi báo cáo)
            $table->string('anh_sau')->nullable()->after('anh');
            // Đánh giá của sinh viên sau khi thanh toán
            $table->unsignedTinyInteger('rating')->nullable()->comment('1-5 sao')->after('is_paid');
            $table->text('feedback')->nullable()->after('rating');
            $table->timestamp('rated_at')->nullable()->after('feedback');
        });
    }

    public function down(): void
    {
        Schema::table('su_co', function (Blueprint $table) {
            $table->dropColumn(['completion_percent', 'anh_sau', 'rating', 'feedback', 'rated_at']);
        });
    }
};


