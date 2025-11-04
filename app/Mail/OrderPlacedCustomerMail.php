<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;

class OrderPlacedCustomerMail extends Mailable
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this
            ->subject('Поръчката ви е приета — #' . $this->order->id)
            ->view('emails.orders.customer_plain')
            ->with(['order' => $this->order]);
    }
}
