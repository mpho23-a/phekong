<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Product $product) {}


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
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Low Stock Alert: {$this->product->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->product->name} is running low on stock.")
            ->line("Current quantity: {$this->product->quantity}")
            ->line("Threshold: {$this->product->low_stock_threshold}")
            ->action('View Products', route('products.index'))
            ->line('Please review and restock as needed.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => $this->product->quantity,
            'threshold' => $this->product->low_stock_threshold,
            'message' => "{$this->product->name} is low on stock ({$this->product->quantity} left).",
        ];
    }
}
