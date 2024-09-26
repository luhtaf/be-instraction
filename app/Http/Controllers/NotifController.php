<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use NotificationChannels\Telegram\TelegramMessage;
use Illuminate\Notifications\Notification;

class NotifController extends Notification
{
    // protected $telegram;

    /**
     * Create a new controller instance.
     *
     * @param  Api  $telegram
     */
    public function via($notifiable)
    {
        return ["telegram"];
    }

    public function toTelegram($notifiable)
    {
        $url = url('/invoice/' . $this->invoice->id);

        return TelegramMessage::create()
            // Optional recipient user id.
            ->to($notifiable->telegram_user_id)
            // Markdown supported.
            ->content("Hello there!")
            ->line("Your invoice has been *PAID*")
            ->lineIf($notifiable->amount > 0, "Amount paid: {$notifiable->amount}")
            ->line("Thank you!")

            // (Optional) Blade template for the content.
            // ->view('notification', ['url' => $url])

            // (Optional) Inline Buttons
            ->button('View Invoice', $url)
            ->button('Download Invoice', $url)
            // (Optional) Inline Button with callback. You can handle callback in your bot instance
            ->buttonWithCallback('Confirm', 'confirm_invoice ' . $this->invoice->id);
    }
}
