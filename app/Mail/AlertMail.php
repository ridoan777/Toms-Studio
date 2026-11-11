<?php

namespace App\Mail;

use App\Models\System\Settings\OptionSiteSetup;
use App\Models\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Address, Content, Envelope, Attachment};

use Illuminate\Queue\SerializesModels;

class AlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected ?User $targetUser = null,
        protected ?string $customSubject = null,
        protected ?string $bodyTitle = null,
        protected ?string $bodyContent = null,
        protected $hasAttachment = false,
        protected $attachmentSource = "path",   // path | disk_storage
        protected $attachmentPath = null,
        protected $attachmentName = null,
        protected $attachmentType = null,
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $this->customSubject ?? 'Alert Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.system.alert',
            with: [
                'targetUserName' => $this->targetUser?->name,
                'targetUserEmail' => $this->targetUser?->email,
                'setLogo' => $this->getLogo(),
                'bodyTitle' => $this->bodyTitle,
                'bodyContent' => $this->bodyContent,
                'attachmentPath' => ($this->hasAttachment && !empty($this->attachmentPath)) ? $this->attachmentPath : null,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (!$this->hasAttachment || empty($this->attachmentPath))
            return [];

        return [
            $this->attachmentSource === 'path'
            ? Attachment::fromPath($this->attachmentPath)
                ->as($this->attachmentName ?? basename($this->attachmentPath))
                ->withMime($this->attachmentType ?? 'application/octet-stream')

            : ($this->attachmentSource === 'url'
                ? Attachment::fromUrl($this->attachmentPath)
                    ->as($this->attachmentName ?? basename(parse_url($this->attachmentPath, PHP_URL_PATH)))
                    ->withMime($this->attachmentType ?? 'application/octet-stream')

                : Attachment::fromStorageDisk($this->attachmentSource, $this->attachmentPath)
                    ->as($this->attachmentName ?? basename($this->attachmentPath))
                    ->withMime($this->attachmentType ?? 'application/octet-stream')
            )
        ];
    }

    private function getLogo()
    {
        $logo = OptionSiteSetup::type('site_basic')->name('site_logo')->first();
        return $logo ? asset($logo->value) : asset('site_assets/logo_n_icons/app_logo.png');
    }
}
/*
   DISK_FOLDER = config('filesystems.default');

   Mail::to($originalUser?->email ?? 'fallback@example.com')->send(new AlertMail($originalUser, "Email change alert", "Your email {$originalUser->email} has been changed to {$secondEmail} {$adminChangeMessage}.", "If you are not the one who did this, please check.", true, $DISK_FOLDER, $originalUser->avatar));

*/