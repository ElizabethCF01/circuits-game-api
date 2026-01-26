<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PasswordResetEmail extends Mailable
{

    public function __construct(
        public User $user,
        public string $token
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.password-reset',
            with: [
                'resetUrl' => config('app.frontend_url', config('app.url')).'/reset-password?token='.$this->token.'&email='.urlencode($this->user->email),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
