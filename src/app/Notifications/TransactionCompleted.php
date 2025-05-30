<?php

namespace App\Notifications;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TransactionCompleted extends Notification
{
    use Queueable;

    protected Purchase $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $item      = $this->purchase->item;
        $buyerName = $this->purchase->user->profile->name ?? $this->purchase->user->name;

        return (new MailMessage)
            ->subject('【Flea Market】取引が完了しました')
            ->greeting("こんにちは、{$notifiable->profile->name} さん")
            ->line("「{$item->name}」の取引が購入者 {$buyerName} さんによって完了されました。")
            ->action('取引詳細を見る', url(route('chat_rooms.show', $this->purchase->chatRoom)))
            ->line('引き続きよろしくお願いいたします。');
    }
}
