<?php

namespace App\Livewire\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\CheckoutValidation;
use Illuminate\Support\Facades\Mail;
use App\Services\Shipping\EcontDirectoryService;
use App\Services\Shipping\ShippingCalculator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Checkout extends Component
{
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $payment_method = 'card';
    public $terms_accepted = false;

    public $invoiceRequested = false;
    public $companyName = '';
    public $companyID = '';
    public $companyAddress = '';
    public $companyTaxNumber = '';
    public $companyMol = '';

    public $cartItems = [];
    public float $shippingCost = 0;
    public float $totalWithShipping = 0;

    # Econt shipping method: office / address
    public string $shipping_method = 'address';

    # City
    public string $citySearch = '';
    public ?int $cityId = null;
    public string $cityLabel = '';
    public ?string $cityPostCode = null;
    public array $cityOptions = [];

    # Street
    public string $streetSearch = '';
    public ?int $streetId = null;
    public ?int $streetCode = null;
    public string $streetLabel = '';
    public array $streetOptions = [];
    public string $streetNum = '';

    # Office
    public string $officeSearch = '';
    public ?string $officeCode = null;
    public string $officeLabel = '';
    public array $officeOptions = [];

    public function mount()
    {
        $this->cartItems = session('cart', []);
        if (!$this->cartItems) {
            return redirect()->to('/');
        }
        $this->calculateLiveShipping();
    }

    private function dir(): EcontDirectoryService
    {
        return app(EcontDirectoryService::class);
    }

    # ===== OFFICE SEARCH =====
    public function updatedOfficeSearch()
    {
        $q = trim($this->officeSearch);
        if ($q === '' || ($this->officeCode && $q === $this->officeLabel)) {
            $this->officeOptions = [];
            return;
        }

        $this->officeOptions = array_values(
            $this->dir()->searchOffices($q, 200)->toArray()
        );
    }

    public function selectOffice($code, $label)
    {
        $this->officeCode = $code;
        $this->officeLabel = $label;
        $this->officeSearch = $label;
        $this->officeOptions = [];
        $this->calculateLiveShipping();
    }

    # ===== CITY SEARCH =====
    public function updatedCitySearch()
    {
        $q = trim($this->citySearch);
        if ($q === '' || ($this->cityId && $q === $this->cityLabel)) {
            $this->cityOptions = [];
            return;
        }

        $this->cityOptions = array_values(
            $this->dir()->searchCities($q, 50)->map(fn($c) => [
                'id' => $c['id'],
                'label' => $c['label'],
                'post_code' => $c['post_code'] ?? null,
            ])->toArray()
        );
    }

    public function selectCity($id, $label, $postCode = null)
    {
        $this->cityId = $id;
        $this->cityLabel = $label;
        $this->cityPostCode = $postCode;
        $this->citySearch = $label;
        $this->cityOptions = [];
        $this->calculateLiveShipping();
    }

    # ===== STREET SEARCH =====
    public function updatedStreetSearch()
    {
        if (!$this->cityId) return;

        $q = trim($this->streetSearch);
        if ($q === '' || ($this->streetId && $q === $this->streetLabel)) {
            $this->streetOptions = [];
            return;
        }

        $this->streetOptions = array_values(
            $this->dir()->streetsByCity($this->cityId, $q, 100)->toArray()
        );
    }

    public function selectStreet($id, $code, $label)
    {
        $this->streetId = $id;
        $this->streetCode = $code;
        $this->streetLabel = $label;
        $this->streetSearch = $label;
        $this->streetOptions = [];
        $this->calculateLiveShipping();
    }

    public function updatedStreetNum()
    {
        $this->calculateLiveShipping();
    }

    public function submitOrder()
    {
        \Log::debug('Checkout: submitOrder triggered', $this->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'shipping_method'
        ]));

        $validator = Validator::make(
            $this->only([
                'first_name',
                'last_name',
                'email',
                'phone',
                'terms_accepted',
                'shipping_method',
                'cityId',
                'streetCode',
                'streetNum',
                'officeCode'
            ]),
            CheckoutValidation::rules($this->shipping_method, $this->invoiceRequested),
            CheckoutValidation::messages()
        );


        if ($validator->fails()) {
            \Log::warning('Checkout: validation failed', $validator->errors()->toArray());

            foreach ($validator->errors()->messages() as $field => $messages) {
                foreach ($messages as $message) $this->addError($field, $message);
            }
            return;
        }

        \Log::info('Checkout: validation passed, creating order');

        return $this->createOrder();
    }


    private function calcWeight()
    {
        return collect($this->cartItems)->sum(fn($i) => ($i['weight'] ?? 0.5) * $i['quantity']);
    }

    public function calculateLiveShipping()
    {
        if (!$this->first_name || !$this->phone) return;

        $weight = max(0.3, $this->calcWeight());
        $calc = app(ShippingCalculator::class);

        try {
            $this->shippingCost = $calc->calculate([
                'sender' => [
                    'name' => config('shipping.econt.sender_name'),
                    'phone' => config('shipping.econt.sender_phone'),
                    'city_name' => config('shipping.econt.sender_city'),
                    'post_code' => config('shipping.econt.sender_post'),
                    'street' => config('shipping.econt.sender_street'),
                    'num' => config('shipping.econt.sender_num'),
                ],
                'receiver' => [
                    'name' => $this->first_name . ' ' . $this->last_name,
                    'phone' => preg_replace('/\s+/', '', $this->phone),
                    'city_id' => $this->shipping_method === 'address' ? $this->cityId : null,
                    'office_code' => $this->shipping_method === 'econt_office' ? $this->officeCode : null,
                    'street_label' => $this->streetLabel,
                    'street_num' => $this->streetNum,
                ],
                'pack_count' => 1,
                'weight' => $weight,
                'description' => 'Хранителни добавки',
            ]);
        } catch (\Throwable $e) {
            $this->shippingCost = 0;
        }

        $subtotal = collect($this->cartItems)->sum(fn($i) => $i['price'] * $i['quantity']);
        $this->totalWithShipping = $subtotal + $this->shippingCost;
    }

    private function createOrder()
    {
        \Log::info('Checkout: createOrder() hit', [
            'payment_method' => $this->payment_method,
        ]);

        return DB::transaction(function () {

            $items    = collect($this->cartItems);
            $subtotal = $items->sum(fn($i) => $i['price'] * $i['quantity']);
            $weight   = $this->calcWeight();
            $total    = $subtotal + $this->shippingCost;

            $order = Order::create([
                'first_name' => $this->first_name,
                'last_name'  => $this->last_name,
                'email'      => $this->email,
                'phone'      => $this->phone,
                'payment_method' => $this->payment_method,
                'total'      => $total,
                'weight'     => $weight,

                // SHIPPING
                'city'           => $this->cityLabel,
                'zip'            => $this->cityPostCode,
                'street'         => $this->streetLabel,
                'street_num'     => $this->streetNum,
                'office_code'    => $this->officeCode,
                'shipping_method' => $this->shipping_method,

                // INVOICE
                'invoice' => $this->invoiceRequested ? [
                    'company_name'     => $this->companyName,
                    'company_id'       => $this->companyID,
                    'company_address'  => $this->companyAddress,
                    'company_tax_number' => $this->companyTaxNumber,
                    'company_mol'      => $this->companyMol,
                ] : null,

                'terms_accepted'     => $this->terms_accepted,
                'terms_accepted_at'  => now(),
                'status'             => 'pending'
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'       => $order->id,
                    'product_id'     => $item['id'],
                    'product_source' => $item['source'] ?? null,
                    'product_slug'   => $item['slug'] ?? null,
                    'product_image'  => $item['image'] ?? null,
                    'name'           => $item['name'],
                    'price'          => $item['price'],
                    'quantity'       => $item['quantity'],
                    'currency'       => $item['currency'] ?? 'лв.',
                ]);
            }

            session()->put('checkout_order_id', $order->id);

            /**
             * COD FLOW — generate label immediately
             */
            if ($this->payment_method === 'cod') {

                try {
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
                            'name'        => $order->first_name . ' ' . $order->last_name,
                            'phone'       => preg_replace('/\s+/', '', $order->phone),
                            'city_id'     => $this->shipping_method === 'address' ? $this->cityId : null,
                            'office_code' => $this->shipping_method === 'econt_office' ? $this->officeCode : null,
                            'street_label' => $this->streetLabel ?: null,
                            'street_num'  => $this->streetNum ?: null,
                        ],
                        'pack_count'  => 1,
                        'weight'      => $weight,
                        'description' => 'Хранителни добавки',
                        'cod' => [
                            'amount'   => $order->total,
                            'type'     => 'get',
                            'currency' => 'BGN',
                        ],
                    ]);

                    $order->update([
                        'status'            => 'paid',
                        'payment_status'    => 'paid',
                        'paid_at'           => now(),
                        'shipping_provider' => 'econt',
                        'shipping_payload'  => $label,
                    ]);

                    Mail::to($order->email)->send(new \App\Mail\OrderPlacedCustomerMail($order));
                    Mail::to(config('mail.admin_address'))->send(new \App\Mail\OrderPlacedAdminMail($order));
                } catch (\Throwable $e) {
                    \Log::error('COD Econt error: ' . $e->getMessage());
                }

                session()->forget('cart');

                return redirect()->route('thank-you');
            }

            /**
             * STRIPE FLOW
             */
            if ($this->payment_method === 'card') {
                \Log::info('Checkout: redirecting to Stripe', ['order_id' => $order->id]);
                return redirect()->route('stripe.create', ['order' => $order->id]);
            }

            // fallback
            session()->forget('cart');
            return redirect()->route('thank-you');
        });
    }



    public function render()
    {
        return view('livewire.pages.checkout', [
            'items' => collect($this->cartItems)
        ])->layout('layouts.app');
    }
}
