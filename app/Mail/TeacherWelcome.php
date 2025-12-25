<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeacherWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $teacherName;
    public $email;
    public $password;
    public $schoolName;

    /**
     * Create a new message instance.
     */
    public function __construct($teacherName, $email, $password, $schoolName)
    {
        $this->teacherName = $teacherName;
        $this->email = $email;
        $this->password = $password;
        $this->schoolName = $schoolName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . $this->schoolName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.teacher_welcome',
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
