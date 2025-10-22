<?php

namespace App\Exceptions;

use Exception;

class PhongException extends Exception
{
    public static function tenPhongTrung(string $tenPhong): self
    {
        return new self("Tên phòng \"{$tenPhong}\" đã tồn tại.", 422);
    }

    public static function uploadFailed(string $reason): self
    {
        return new self("Upload hình ảnh thất bại: {$reason}.", 422);
    }

    public static function phongBaoTri(string $tenPhong): self
    {
        return new self("Phòng {$tenPhong} đang bảo trì, không thể thao tác.", 422);
    }
}


