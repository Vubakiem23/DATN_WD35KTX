<?php

namespace App\Mail;

use App\Models\SinhVien;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SinhVienApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SinhVien $sinhVien,
        public string $confirmationUrl
    ) {
    }

    public function build(): self
    {
        return $this->subject('Hồ sơ ký túc xá của bạn đã được duyệt')
            ->view('emails.sinhvien.approved');
    }
}

