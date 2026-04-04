<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ItemStatusNotification extends Notification
{
    use Queueable;

    protected $item;
    protected $reason;

    public function __construct($item, string $reason)
    {
        $this->item = $item;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        // For now → always send email (we’ll refine later)
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('LabSync Notification')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have a new notification.')
            ->line('Reason: ' . $this->reason)
            ->line('Item: ' . ($this->item->name ?? 'Unknown Item'))
            ->line('Thank you for using LabSync!');
    }
}
