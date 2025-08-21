<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Notifications\Notification;

class ReminderCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $title, public string $body, public ?int $reminderId=null)
    {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toWebPush(object $notifiable, object $notification): WebPushMessage
    {
        $url = route('admin.crm.reminders.index');
        $tag = 'reminder-'.$this->reminderId;
        $actions = [
            ['action' => 'open', 'title' => 'Ver'],
            ['action' => 'dismiss', 'title' => 'Descartar']
        ];
        return (new WebPushMessage)
            ->title($this->title)
            ->icon(asset('images/logo.png'))
            ->badge(asset('images/logo.png'))
            ->body($this->body)
            ->vibrate([200,80,200])
            ->tag($tag)
            ->data([
                'url' => $url,
                'reminder_id' => $this->reminderId,
                'tag' => $tag,
                'actions' => $actions
            ])
            ->action('Ver', 'open')
            ->action('Descartar', 'dismiss')
            ->options(['renotify' => true]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'reminder_id' => $this->reminderId,
        ];
    }
}
