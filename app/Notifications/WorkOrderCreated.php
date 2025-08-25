<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class WorkOrderCreated extends Notification
{
    use Queueable;

    public function __construct(
        public int $orderId,
        public string $customerName,
        public array $technicianNames,
    ){}

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, object $notification): WebPushMessage
    {
        $url = route('admin.work-orders.show', $this->orderId);
        $techs = implode(', ', $this->technicianNames);
        return (new WebPushMessage)
            ->title('Nueva Orden de Trabajo')
            ->body('Cliente: '.$this->customerName.' | Técnicos: '.$techs)
            ->icon(asset('logo.png'))
            ->badge(asset('logo.png'))
            ->data([
                'url' => $url,
                'work_order_id' => $this->orderId,
                'tag' => 'work-order-created',
            ])
            ->tag('work-order-created')
            ->action('Ver orden', 'open');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'work_order_id' => $this->orderId,
            'customer' => $this->customerName,
            'technicians' => $this->technicianNames,
        ];
    }
}
