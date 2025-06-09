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
    // === User input properties ===
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $city = '';
    public $zip = '';
    public $street = '';
    public $payment_method = 'card'; // Default is Stripe payment

    // === Invoice fields ===
    public $invoiceRequested = false;
    public $companyName = '';
    public $companyID = '';
    public $companyAddress = '';
    public $companyTaxNumber = '';
    public $companyMol = '';

    public bool $terms_accepted = false;

    // === Cart items from session ===
    public $cartItems = [];

    // === Stripe event listener ===
    protected $listeners = ['stripeTokenReceived'];

    // Load cart items from session
    public function mount()
    {
        $this->cartItems = session('cart', []);
    }

    // Stripe token received from frontend
    public function stripeTokenReceived($token = null)
    {
        $this->pay($token);
    }

    // Main checkout logic
    public function pay($token = null)
    {
        DB::beginTransaction();

        try {
            // === Validate form input ===
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
                // Pass errors to Livewire validation system
                foreach ($validator->errors()->messages() as $field => $messages) {
                    foreach ($messages as $message) {
                        $this->addError($field, $message);
                    }
                }
                $this->dispatch('$refresh');
                return;
            }

            // === Get product info and subtotal ===
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

            $subtotal = $items->sum(fn($item) => (float) $item['price'] * $item['quantity']);

            // === Temporary order object used for Econt shipping calculation ===
            $tempOrder = new Order([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone' => $this->phone,
                'city' => $this->city,
                'zip' => $this->zip,
                'street' => $this->street,
                'payment_method' => $this->payment_method,
            ]);

            $shippingCost = EcontLabelService::calculateShipping($tempOrder);
            if (is_null($shippingCost)) {
                $this->addError('shipping', 'Shipping cost calculation failed.');
                return;
            }

            $total = $subtotal + $shippingCost;

            // === Save order to DB ===
            $order = Order::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'city' => $this->city,
                'zip' => $this->zip,
                'street' => $this->street,
                'payment_method' => $this->payment_method,
                'total' => $total,
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

            // === Save each order item ===
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

            // === Process Stripe payment ===
            if ($this->payment_method === 'card') {
                if (!$token) {
                    $this->addError('stripe', 'Invalid Stripe token.');
                    return;
                }

                Stripe::setApiKey(config('services.stripe.secret'));

                PaymentIntent::create([
                    'amount' => $total * 100, // amount in cents
                    'currency' => 'bgn',
                    'payment_method' => $token,
                    'confirm' => true,
                    'description' => 'Order from ' . $this->first_name . ' ' . $this->last_name,
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

            // === Create Econt shipping label ===
            EcontLabelService::createLabel($order, $total);

            DB::commit();

            // === Finalize and redirect ===
            $this->dispatch('orderComplete');
            session()->forget('cart');
            session()->flash('success', 'Order was successfully placed!');
            return redirect()->to('/thank-you');
        } catch (\Throwable $e) {
            // === Handle any exceptions ===
            DB::rollBack();

            \Log::error('❌ Checkout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $response = json_decode($e->getResponse()?->getBody(), true);
                \Log::error('📨 API response error', $response ?? []);
            }

            $this->addError('general', 'Something went wrong. Please try again.');
            return null;
        }
    }

    // === Render the checkout page view ===
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
