<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ExpenseCreated extends Notification
{
    use Queueable;

    public function __construct(
        public int $expenseId,
        public string $technicianName,
        public float $amount,
        public ?string $description = null,
    ) {}

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, object $notification): WebPushMessage
    {
        $url = route('admin.expenses.show', $this->expenseId);
        return (new WebPushMessage)
            ->title('Nuevo gasto registrado')
            ->body($this->technicianName . ' registró Q' . number_format($this->amount,2) . ' - ' . ($this->description ?: 'Sin descripción'))
            ->icon(asset('logo.png'))
            ->badge(asset('logo.png'))
            ->data([
                'url' => $url,
                'expense_id' => $this->expenseId,
                'tag' => 'expense-created',
            ])
            ->tag('expense-created')
            ->action('Ver gasto', 'open');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'expense_id' => $this->expenseId,
            'technician' => $this->technicianName,
            'amount' => $this->amount,
        ];
    }
}
