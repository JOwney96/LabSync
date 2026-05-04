<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemStatusNotification extends Notification
{
    use Queueable;

    protected $equipment;
    protected $type;
//    protected $item;
//    protected $reason;

    public function __construct($equipment, $type /*$item, $reason*/)
    {
        $this->equipment = $equipment;
        $this->type = $type;
//        $this->item = $item;
//        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail']; // sends email
    }

    public function toMail($notifiable)
    {
        $equipmentName = $this->equipment->name ?? 'your item';

        return match ($this->type) {
            'due_week' => (new MailMessage)
                ->subject('Reminder: Item Due in 7 Days')
                ->line("The item '{$equipmentName}' is due in 7 days.")
                ->line('Please return it on time.'),
            'due_today' => (new MailMessage)
                ->subject('Reminder: Item Due Today')
                ->line("The item '{$equipmentName}' is due today.")
                ->line('Please return it as soon as possible.'),
            'overdue' => (new MailMessage)
                ->subject('Overdue Item Notice')
                ->line("The item '{$equipmentName}' is overdue.")
                ->line('Please return it immediately.'),
            default => (new MailMessage)
                ->subject('Item Update')
                ->line("Update regarding '{$equipmentName}'."),
        };
    }

    /*public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('LabSync Notification')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have a new notification.')
            ->line('Reason: ' . $this->reason)
            ->line('Item: ' . ($this->item->name ?? 'Unknown Item'))
            ->line('Thank you for using LabSync!');
    }*/
}
