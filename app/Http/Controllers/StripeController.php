<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Order;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function create(Order $order)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'bgn',
                    'product_data' => ['name' => 'Поръчка #' . $order->id],
                    'unit_amount' => $order->total * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel'),
            'metadata' => ['order_id' => $order->id],
        ]);

        return redirect($session->url);
    }

    public function success(Request $req)
    {
        $orderId = Order::where('id', session('checkout_order_id'))->first();
        if ($orderId) {
            $orderId->update(['status' => 'paid']);
            session()->forget('cart');
            session()->forget('checkout_order_id');
        }

        return redirect('/thank-you');
    }

    public function cancel()
    {
        return redirect('/cart')->with('error', 'Плащането е отказано.');
    }
}
