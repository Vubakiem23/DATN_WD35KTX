<?php

namespace App\Mail;

use App\Models\SinhVien;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SinhVien $sinhVien
    ) {
    }

    public function build(): self
    {
        return $this->subject('Đăng ký ký túc xá thành công')
            ->view('emails.registration_success');
    }
}

