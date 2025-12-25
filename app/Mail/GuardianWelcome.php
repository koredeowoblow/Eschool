<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuardianWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $guardianName;
    public $guardianEmail;
    public $guardianPassword;
    public $studentName;
    public $studentPassword;
    public $studentEmail;
    public $schoolName;

    /**
     * Create a new message instance.
     */
    public function __construct($guardianName, $guardianEmail, $guardianPassword, $studentName, $studentPassword, $studentEmail, $schoolName)
    {
        $this->guardianName = $guardianName;
        $this->guardianEmail = $guardianEmail;
        $this->guardianPassword = $guardianPassword;
        $this->studentName = $studentName;
        $this->studentPassword = $studentPassword;
        $this->studentEmail = $studentEmail;
        $this->schoolName = $schoolName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome - Student Account Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.guardian_welcome',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
