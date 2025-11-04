<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Order;
use App\Support\Cart;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedCustomerMail;
use App\Mail\OrderPlacedAdminMail;

class ThankYou extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $this->order = $order;

        // Stripe success logic
        if (request('session_id') && $this->order->payment_method === 'card' && $this->order->status !== 'paid') {
            $this->confirmStripe(request('session_id'));
        }

        // If COD — email already sent in checkout, just clear cart
        if ($this->order->payment_method === 'cod') {
            Cart::clear();
        }
    }

    private function confirmStripe($sessionId)
    {
        try {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            if (($session->payment_status ?? null) !== 'paid') {
                return;
            }

            $this->order->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => now()
            ]);

            // Create Econt label
            $this->createEcontLabel();

            // Send emails
            Mail::to($this->order->email)->send(new OrderPlacedCustomerMail($this->order));
            Mail::to(config('mail.admin_address'))->send(new OrderPlacedAdminMail($this->order));

            Cart::clear();
        } catch (\Throwable $e) {
            \Log::error('Stripe confirm fail: ' . $e->getMessage());
        }
    }

    private function createEcontLabel()
    {
        try {
            $draft = $this->order->shipping_draft;

            if (!$draft || !is_array($draft)) {
                \Log::warning('ThankYou: no draft for Econt', ['order' => $this->order->id]);
                return;
            }

            $labelService = app(\App\Services\Shipping\EcontLabelService::class);

            $label = $labelService->validateThenCreate([
                'sender' => [
                    'name'      => config('shipping.econt.sender_name'),
                    'phone'     => config('shipping.econt.sender_phone'),
                    'city_name' => config('shipping.econt.sender_city'),
                    'post_code' => config('shipping.econt.sender_post'),
                    'street'    => config('shipping.econt.sender_street'),
                    'num'       => config('shipping.econt.sender_num'),
                ],
                'receiver' => [
                    'name'        => $draft['receiver']['name'] ?? $this->order->first_name . ' ' . $this->order->last_name,
                    'phone'       => preg_replace('/\s+/', '', $draft['receiver']['phone'] ?? $this->order->phone),
                    'city_id'     => $draft['receiver']['city_id'] ?? null,
                    'office_code' => $draft['receiver_office_code'] ?? null,
                    'street_label' => $draft['receiver']['street_label'] ?? null,
                    'street_num'  => $draft['receiver']['street_num'] ?? null,
                ],
                'pack_count' => 1,
                'weight'     => $draft['weight'] ?? ($this->order->weight ?? 0.5),
                'description' => $draft['description'] ?? 'Хранителни добавки',
            ]);

            $this->order->update([
                'shipping_provider' => 'econt',
                'shipping_payload'  => $label,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Econt label error: ' . $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.pages.thank-you', [
            'order' => $this->order
        ])->layout('layouts.app');
    }
}
