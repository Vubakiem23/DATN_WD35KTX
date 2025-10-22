<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::table('hoa_don', function (Blueprint $table) {
            // Kiểm tra nếu tồn tại khóa ngoại thì mới xóa
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = $sm->listTableForeignKeys('hoa_don');

            foreach ($foreignKeys as $fk) {
                if ($fk->getLocalColumns() === ['sinh_vien_id']) {
                    $table->dropForeign($fk->getName());
                }
            }

            // Kiểm tra nếu cột tồn tại thì mới xóa
            if (Schema::hasColumn('hoa_don', 'sinh_vien_id')) {
                $table->dropColumn('sinh_vien_id');
            }
        });
    }
    public function down(): void
    {
        
    }
};
