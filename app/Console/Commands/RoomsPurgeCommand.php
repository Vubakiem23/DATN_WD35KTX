<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RoomsPurgeCommand extends Command
{
    protected $signature = 'rooms:purge {--yes : Skip confirmation}';
    protected $description = 'Xóa sạch dữ liệu phòng: slots, tài sản thuộc phòng, bỏ liên kết sinh viên, rồi xóa toàn bộ phòng';

    public function handle(): int
    {
        if (!$this->option('yes')) {
            if (!$this->confirm('Bạn chắc chắn muốn XÓA TOÀN BỘ dữ liệu phòng? Thao tác không thể hoàn tác!', false)) {
                $this->warn('Đã hủy.');
                return self::SUCCESS;
            }
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Bỏ liên kết phòng khỏi sinh viên
            if (DB::getSchemaBuilder()->hasColumn('sinh_vien', 'phong_id')) {
                DB::table('sinh_vien')->update(['phong_id' => null]);
            }

            // Xóa slots
            if (DB::getSchemaBuilder()->hasTable('slots')) {
                DB::table('slots')->truncate();
            }

            // Xóa tài sản thuộc phòng
            if (DB::getSchemaBuilder()->hasTable('tai_san') && DB::getSchemaBuilder()->hasColumn('tai_san','phong_id')) {
                DB::table('tai_san')->whereNotNull('phong_id')->delete();
            }

            // Xóa phòng
            if (DB::getSchemaBuilder()->hasTable('phong')) {
                DB::table('phong')->truncate();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->info('Đã xóa sạch dữ liệu phòng.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error('Lỗi khi xóa dữ liệu phòng: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}


