<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Notifications\Notification;

class TechnicianEvent extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $title,
        public string $body,
        public array $data = []
    ) {}

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
        $url = $this->data['url'] ?? route('admin.work-orders.index');
        $tag = $this->data['tag'] ?? 'tech-event';
        $actions = [
            ['action' => 'open', 'title' => 'Abrir'],
            ['action' => 'close', 'title' => 'Cerrar']
        ];
        return (new WebPushMessage)
            ->title($this->title)
            ->icon(asset('images/logo.png'))
            ->badge(asset('images/logo.png'))
            ->body($this->body)
            ->vibrate([100,50,100])
            ->tag($tag)
            ->data(array_merge([
                'url' => $url,
                'ts'  => now()->timestamp,
                'tag' => $tag,
                // actions se añaden fuera; mantenemos meta por si se usa internamente
                'actions' => $actions
            ], $this->data))
            ->action('Abrir', 'open')
            ->action('Cerrar', 'close')
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
            'title'=>$this->title,
            'body'=>$this->body,
        ] + $this->data;
    }
}
