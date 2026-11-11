<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOTPMail extends Notification
{
    use Queueable;

    private $otp;
    private $type;
    private $expiryTime;

    public function __construct($otp, $type, $expiryTime)
    {
        $this->otp = $otp;
        $this->type = $type;
        $this->expiryTime = $expiryTime;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $frontendUrl = env('FRONTEND_URL');

        // ---------------- SUBJECT ----------------
        $subject = match ($this->type) {
            'email_verification' => 'Verify Your Email Address',
            'password_setup' => 'Welcome! Set Your Password',
            default => 'Reset Your Password',
        };

        // ---------------- MESSAGE ----------------
        $message = match ($this->type) {
            'email_verification' => 'Use this OTP to verify your email address:',
            'password_setup' => 'Your account has been created. Click from the button below. Use this email, an OTP will be send & then use that OTP toset a new password ',
            default => 'Use this OTP to reset your password:',
        };

        // ---------------- LINK ----------------
        $actionUrl = match ($this->type) {
            // 'password_setup' => "{$frontendUrl}/enter-code",
            'password_setup' => "{$frontendUrl}/forgot-password",
            default => "{$frontendUrl}/reset-password",
        };

        $actionText = match ($this->type) {
            'email_verification' => 'Verify Email',
            'password_setup' => 'Set Password',
            default => 'Reset Password',
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line('Welcome to ' . config('app.name') . '.')
            ->line($message)
            ->line($this->type === 'password_setup' ? null : 'OTP: ' . $this->otp)
            ->line($this->type === 'password_setup' ? null : "This OTP will expire in {$this->expiryTime} minutes.")
            ->action($actionText, $actionUrl)
            ->line('If this was not requested by you, you can safely ignore this email.');
    }

}
