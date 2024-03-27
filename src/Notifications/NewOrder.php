<?php

namespace Atin\LaravelCashierShop\Notifications;

use Atin\LaravelCashierShop\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class NewOrder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Order $order
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $additionalUserInfo = '';

        if ($this->order->user->locale) {
            $additionalUserInfo .= $additionalUserInfo ? ' · '.$this->order->user->locale : $this->order->user->locale;
        }

        if ($this->order->user->country) {
            $additionalUserInfo .= $additionalUserInfo ? ' · '.$this->order->user->country : $this->order->user->country;
        }

        if ($this->order->user->variant) {
            $additionalUserInfo .= $additionalUserInfo ? ' · '.$this->order->user->variant : $this->order->user->variant;
        }

        if ($this->order->user->keyword) {
            $additionalUserInfo .= $additionalUserInfo ? ' · '.$this->order->user->keyword : $this->order->user->keyword;
        }

        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->line((app()->isProduction() ? '' : 'TEST ').'*[New Order]*')
            ->line('_ID:_ '.$this->order->id)
            ->line('_Product:_ '.$this->order->product->name)
            ->line('_Status:_ '.$this->order->product->status->value)
            ->line('_Quantity:_ '.$this->order->quantity)
            ->line('_User ID:_ '.$this->order->user->id)
            ->line('_User Email:_ '.$this->order->user->email)
            ->line($additionalUserInfo);
    }
}
