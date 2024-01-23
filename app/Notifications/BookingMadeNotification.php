<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingMadeNotification extends Notification
{
    use Queueable;

    public $booking;

    public $room;

    /**
     * Create a new notification instance.
     */
    public function __construct($booking, $room)
    {
        $this->booking = $booking;
        $this->room = $room;
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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('New Booking Created!')
            ->line('A new booking has been made.')
            ->line('Booking Details:')
            ->line('Room Number: ' . $this->room['number'])
            ->line('Thank you for using our hotel booking system!')
            ->salutation('Best regards, Hotel Staff');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
