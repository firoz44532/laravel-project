<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOrderNotification extends Notification
{
    use Queueable;

    private $order;

    /**
     * Create a new notification instance.
     */
    public function __construct($order = null)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order;
        
        return (new MailMessage)
            ->subject('Order Confirmation - ' . $order->order_number)
            ->greeting('Dear ' . $order->user->name)
            ->line('Thank you for your order!')
            ->line('Order #' . $order->order_number)
            ->line('Status: ' . ucfirst($order->status))
            ->line('Total Amount: ৳' . number_format($order->total_amount, 2))
            ->line('Payment Method: ' . ucfirst($order->payment->method))
            ->line('Shipping Address: ' . $order->shippingAddress->address_line_1)
            ->line('Billing Address: ' . $order->billingAddress->address_line_1)
            ->line('City: ' . $order->shippingAddress->city . ', ' . $order->shippingAddress->division)
            ->action(route('account.orders.show', ['order_id' => $order->id]), 'View Order Details')
            ->line('Thank you for choosing ShopBD!')
            ->salutation('Best regards,')
            ->line('ShopBD Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'total_amount' => $this->order->total_amount,
            'user_name' => $this->order->user->name,
            'user_email' => $this->order->user->email,
        ];
    }
}
