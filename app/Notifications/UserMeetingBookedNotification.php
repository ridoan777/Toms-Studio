<?php

namespace App\Notifications;

use App\Models\Settings\OptionEmailTemplate;
use Illuminate\Bus\Queueable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class UserMeetingBookedNotification extends Notification implements ShouldQueue
{
	use Queueable;

	/**
	 * Create a new notification instance.
	 */
	public function __construct(
		public readonly string $flag,
		public readonly string $username,
		public readonly string $provider_name,
		public readonly string $user_email,
		public readonly string $provider_email,
		public readonly string $booking_uid,
		public readonly string $datetime,
	) {
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @return array<int, string>
	 */
	public function via(object $notifiable): array
	{
		return ['mail'];
	}

// /*
	public function toMail(mixed $notifiable): MailMessage
	{
		// Fetch template
		$template = OptionEmailTemplate::where('flag', $this->flag)->first();
		
		if (!$template) {
			$template = (object) [
				'subject' => 'New Booking Received!',
				'greeting' => 'Booking received',
				'line_1' => ':name has booked a meeting with you.',
				'line_2' => 'This has been booked for :datetime',
				'line_3' => 'You can email them on :email if you need to organise anything.',
				'button_text' => 'View Here',
			];
		}

		$route = route('api_both_single_hire_request', $this->booking_uid);

			return (new MailMessage)
			->from('info@carmate.com', 'Moises Rivera')
        ->subject($template?->subject ?? "Booking Message")
        ->greeting(new HtmlString($template?->greeting)) // keep this simple
        ->line(new HtmlString($template->body_message ?? ''))
		  ->line("This service was booked for {$this->datetime} between the user:{$this->username} ({$this->user_email}) & the provider: {$this->provider_name} ({$this->provider_email}). The booking ID: {$this->booking_uid}")
        ->line(new HtmlString($template->end_message ?? ''))
        ->line(new HtmlString($template->support_message ?? ''))
        ->line(new HtmlString($template->support_details ?? ''))
        ->action('View Booking', route('api_both_single_hire_request', $this->booking_uid));
	}


	public function toArray(object $notifiable): array
	{
		return [
			//
		];
	}
}



	// */
	/*
	public function toMail(mixed $notifiable): MailMessage
	{
		 //  $url = url('/invoice/'.$this->invoice->id);
		 $route = route('api_both_single_hire_request', $this->booking_uid);
		 return (new MailMessage)
			  ->subject('This is a Subject: New Booking Received.')
			  ->greeting('Greeting! Booking received')
			  ->line("{$this->name} has booked a meeting with you.")
			  ->line("This has been booked for {$this->datetime->format('l jS \\of F Y h:i:s A')}")
			  ->line("You can email them on {$this->email} if you need to organise anything.")
			  ->action('View Here', $route);
	}
	// */