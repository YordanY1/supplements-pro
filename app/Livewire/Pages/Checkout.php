<?php

namespace App\Livewire\Pages;

use App\Mail\NewOrderMail;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Support\CheckoutValidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use App\Services\EcontLabelService;

class Checkout extends Component
{
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $city = '';
    public $zip = '';
    public $street = '';
    public $payment_method = 'card';

    public $invoiceRequested = false;
    public $companyName = '';
    public $companyID = '';
    public $companyAddress = '';
    public $companyTaxNumber = '';
    public $companyMol = '';

    public bool $terms_accepted = false;

    public $cartItems = [];

    protected $listeners = ['stripeTokenReceived'];

    public function mount()
    {
        $this->cartItems = session('cart', []);
    }

    public function stripeTokenReceived($token = null)
    {
        $this->pay($token);
    }

    public function pay($token = null)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make(
                $this->only([
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'city',
                    'zip',
                    'street',
                    'companyName',
                    'companyID',
                    'companyAddress',
                    'companyTaxNumber',
                    'companyMol',
                    'terms_accepted'
                ]),
                CheckoutValidation::rules($this->invoiceRequested),
                CheckoutValidation::messages()
            );

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $field => $messages) {
                    foreach ($messages as $message) {
                        $this->addError($field, $message);
                    }
                }
                $this->dispatch('$refresh');
                return;
            }

            $validated = $validator->validated();

            $products = Product::whereIn('id', array_keys($this->cartItems))->get();
            $items = collect($products)->map(function ($product) {
                $cartItem = $this->cartItems[$product->id] ?? ['quantity' => 1];
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'currency' => $product->currency,
                    'quantity' => (int) $cartItem['quantity'],
                ];
            });

            $amount = $items->sum(fn($item) => (float) $item['price'] * (int) $item['quantity']);

            $order = Order::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'city' => $this->city,
                'zip' => $this->zip,
                'street' => $this->street,
                'payment_method' => $this->payment_method,
                'total' => $amount,
                'invoice' => $this->invoiceRequested ? [
                    'company_name' => $this->companyName,
                    'company_id' => $this->companyID,
                    'company_address' => $this->companyAddress,
                    'company_tax_number' => $this->companyTaxNumber,
                    'company_mol' => $this->companyMol,
                ] : null,
                'terms_accepted' => $this->terms_accepted,
                'terms_accepted_at' => now(),
            ]);

            $response = EcontLabelService::createLabel($order, $amount);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'currency' => $item['currency'],
                    'quantity' => $item['quantity'],
                ]);
            }

            if ($this->payment_method === 'card') {
                if (!$token) {
                    $this->addError('stripe', 'Невалиден Stripe токен.');
                    return;
                }

                Stripe::setApiKey(config('services.stripe.secret'));

                PaymentIntent::create([
                    'amount' => $amount * 100,
                    'currency' => 'bgn',
                    'payment_method' => $token,
                    'confirm' => true,
                    'description' => 'Поръчка от ' . $this->first_name . ' ' . $this->last_name,
                    'metadata' => [
                        'email' => $this->email,
                        'order_id' => $order->id
                    ],
                    'automatic_payment_methods' => [
                        'enabled' => true,
                        'allow_redirects' => 'never',
                    ],
                ]);
            }

            DB::commit();

            $this->dispatch('orderComplete');
            session()->forget('cart');
            session()->flash('success', 'Поръчката беше приета успешно!');
            return redirect()->to('/thank-you');
        } catch (\Throwable $e) {
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $body = json_decode($e->getResponse()->getBody(), true);
                Log::error('Econt response body', $body ?? []);
            }

            Log::error('❌ Грешка при създаване на товарителница: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Грешка при създаване на товарителница: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if (method_exists($e, 'getResponse')) {
                $response = json_decode($e->getResponse()?->getBody(), true);
                Log::error('📨 Econt full response', $response);
            }

            return null;
        }
    }

    public function render()
    {
        $products = Product::whereIn('id', array_keys($this->cartItems))->get();

        $items = $products->map(function ($product) {
            $cartItem = $this->cartItems[$product->id] ?? ['quantity' => 1];
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'currency' => $product->currency?->symbol ?? 'лв.',
                'quantity' => (int) $cartItem['quantity'],
                'image' => $product->image,
                'slug' => $product->slug,
            ];
        });

        $total = $items->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('livewire.pages.checkout', [
            'items' => $items,
            'total' => $total,
        ])->layout('layouts.app');
    }
}
