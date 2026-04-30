<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewAnnouncementNotification extends Mailable {
    public function __construct(
        public string $studentName,
        public string $announcementTitle,
        public string $content,
        public string $startDate
    ) {}

    public function envelope(): Envelope {
        return new Envelope(subject: '📢 New Announcement: ' . $this->announcementTitle);
    }

    public function content(): Content {
        return new Content(view: 'emails.new-announcement');
    }
}