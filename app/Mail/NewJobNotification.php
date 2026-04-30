<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewJobNotification extends Mailable {
    public function __construct(
        public string $studentName,
        public string $jobTitle,
        public string $employerName,
        public string $jobType,
        public string $location
    ) {}

    public function envelope(): Envelope {
        return new Envelope(subject: '🎯 New Job Opportunity: ' . $this->jobTitle);
    }

    public function content(): Content {
        return new Content(view: 'emails.new-job');
    }
}