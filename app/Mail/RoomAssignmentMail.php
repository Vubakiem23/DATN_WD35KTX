<?php

namespace App\Mail;

use App\Models\SinhVien;
use App\Models\RoomAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RoomAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SinhVien $sinhVien,
        public RoomAssignment $assignment,
        public string $confirmationUrl
    ) {
        // Load relationships để đảm bảo email có đủ dữ liệu
        $this->assignment->load('phong.khu');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo gán phòng ký túc xá',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.room_assignment',
            with: [
                'sinhVien' => $this->sinhVien,
                'assignment' => $this->assignment,
                'phong' => $this->assignment->phong,
                'confirmationUrl' => $this->confirmationUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
