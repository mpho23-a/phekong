<?php

namespace App\Notifications;

use App\Models\StockUpdateRequest; 
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockRequestSubmitted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public StockUpdateRequest $stockRequest) {}


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database','mail']; 
    }

    /**
     * Get the mail representation of the notification.
     */
    
public function toMail(object $notifiable): MailMessage
{
    return (new MailMessage)
        ->subject("Stock Update Request: {$this->stockRequest->product->name}")
        ->greeting("Hi {$notifiable->name},")
        ->line("{$this->stockRequest->requester->name} requested a stock update.")
        ->line("Product: {$this->stockRequest->product->name}")
        ->line("Requested quantity: {$this->stockRequest->requested_quantity}")
        ->line("Requested at: {$this->stockRequest->created_at->format('d M Y, H:i')} (SAST)")
        ->action('Review Request', route('stock-requests.index'));
}

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
{
    return [
        'product_id' => $this->stockRequest->product_id,
        'product_name' => $this->stockRequest->product->name,
        'requested_quantity' => $this->stockRequest->requested_quantity,
        'requested_by' => $this->stockRequest->requester->name,
        'requested_at' => $this->stockRequest->created_at->format('d M Y, H:i'),
        'message' => "{$this->stockRequest->requester->name} requested a stock update for {$this->stockRequest->product->name}.",
    ];
}

}
