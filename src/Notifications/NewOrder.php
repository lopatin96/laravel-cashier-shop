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

        if ($this->order->user->country) {
            $additionalUserInfo .= $additionalUserInfo ? ' · '.$this->order->user->country : $this->order->user->country;
        }

        if ($this->order->user->locale) {
            $additionalUserInfo .= $additionalUserInfo ? ' · '.$this->order->user->locale : $this->order->user->locale;
        }

        if ($this->order->user->device) {
            $additionalUserInfo .= $additionalUserInfo ? ' · '.$this->order->user->device : $this->order->user->device;
        }

        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->line((app()->isProduction() ? '' : 'TEST ').'*[New Order]*')
            ->line('_ID:_ '.$this->order->id)
            ->line('_Product:_ '.$this->order->product->name)
            ->line('_Price:_ '.$this->order->product->getDisplayPrice($this->order->user) . ' = ' . $this->order->getAmountInCents()/100 . ' USD')
            ->line('_Paid orders:_ '.$this->order->user->getTotalProcessedOrderCount().' = '.($this->order->user->getTotalProcessedOrderAmountInCents()/100).' USD')
            ->line('_User ID:_ '.$this->order->user->id)
            ->line('_User Email:_ '.$this->order->user->email)
            ->line($additionalUserInfo);
    }
}
