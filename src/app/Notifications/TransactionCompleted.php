<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Purchase;

class TransactionCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * この通知で扱う購入情報を格納するプロパティ
     *
     * @var \App\Models\Purchase
     */
    protected $purchase;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return void
     */
    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * 通知を送るチャネルを指定（この場合は mail）
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * メール送信時の内容を構築する
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // 購入された商品の名前
        $itemName = $this->purchase->item->name;

        // 購入者情報（購入者の「氏名」または「ユーザー名」）
        $buyer = $this->purchase->user;
        $buyerName = optional($buyer->profile)->name ?: $buyer->name;

        // 通知先（出品者）の氏名またはユーザー名
        $seller = $notifiable;
        $sellerName = optional($seller->profile)->name ?: $seller->name;

        // 該当チャットルームの ID（存在する場合のみ取得）
        $chatRoomId = optional($this->purchase->chatRoom)->id;

        return (new MailMessage)
                    // 件名に商品名だけでなく出品者名も含めたい場合は変数を追加できます
                    ->subject("【取引完了】「{$itemName}」の購入者から評価が送信されました")
                    // ここを $notifiable->name ではなく $sellerName に修正
                    ->greeting("こんにちは、{$sellerName} さん")
                    // 本文にも、購入者名ではなく $buyerName を使う
                    ->line("商品「{$itemName}」について、購入者（{$buyerName}）が取引を完了し、評価を送信しました。")
                    ->action(
                        '取引詳細を確認する',
                        // ChatRoom が取得できた場合にのみリンクを生成
                        $chatRoomId
                            ? route('chat_rooms.show', ['chatRoom' => $chatRoomId])
                            : url('/')
                    )
                    ->line('引き続き、取引をよろしくお願いいたします。');
    }
}
