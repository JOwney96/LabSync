<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ItemStatusNotification extends Notification
{
    use Queueable;

    protected $equipment;
    protected $type;

    public function __construct($equipment, $type)
    {
        $this->equipment = $equipment;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail']; // sends email
    }

    public function toMail($notifiable)
    {
        $equipmentName = $this->equipment->name ?? 'your item';

        switch ($this->type) {
            case 'due_week':
                return (new MailMessage)
                    ->subject('Reminder: Item Due in 7 Days')
                    ->line("The item '{$equipmentName}' is due in 7 days.")
                    ->line('Please return it on time.');

            case 'due_today':
                return (new MailMessage)
                    ->subject('Reminder: Item Due Today')
                    ->line("The item '{$equipmentName}' is due today.")
                    ->line('Please return it as soon as possible.');

            case 'overdue':
                return (new MailMessage)
                    ->subject('Overdue Item Notice')
                    ->line("The item '{$equipmentName}' is overdue.")
                    ->line('Please return it immediately.');

            default:
                return (new MailMessage)
                    ->subject('Item Update')
                    ->line("Update regarding '{$equipmentName}'.");
        }
    }
}
