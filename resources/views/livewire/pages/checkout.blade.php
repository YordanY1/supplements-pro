<div class="max-w-xl mx-auto px-4 py-10 font-primary text-[var(--color-text)] bg-[var(--color-card)]">
    <h1 class="text-3xl font-bold text-[var(--color-accent)] mb-8 text-center">Детайли за поръчката</h1>

    <div class="max-w-4xl mx-auto px-4 py-10 font-primary">
        <h1 class="text-3xl font-bold text-[var(--color-accent)] mb-10 text-center">Вашите данни</h1>

        <div class="bg-[var(--color-card)] rounded-xl shadow-lg p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label for="first_name" class="block text-sm font-medium text-[var(--color-text)] mb-1">Име*</label>
                    <input type="text" id="first_name" wire:model.defer="first_name"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] focus:border-transparent">
                    @error('first_name')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="last_name"
                        class="block text-sm font-medium text-[var(--color-text)] mb-1">Фамилия*</label>
                    <input type="text" id="last_name" wire:model.defer="last_name"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] focus:border-transparent">
                    @error('last_name')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-[var(--color-text)] mb-1">Имейл*</label>
                    <input type="email" id="email" wire:model.defer="email"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] focus:border-transparent">
                    @error('email')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-[var(--color-text)] mb-1">Телефонен
                        номер*</label>
                    <input type="text" id="phone" wire:model.defer="phone"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] focus:border-transparent">
                    @error('phone')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="city"
                        class="block text-sm font-medium text-[var(--color-text)] mb-1">Град/Село*</label>
                    <input type="text" id="city" wire:model.defer="city"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] focus:border-transparent">
                    @error('city')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="zip" class="block text-sm font-medium text-[var(--color-text)] mb-1">Пощенски
                        код*</label>
                    <input type="text" id="zip" wire:model.defer="zip"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] focus:border-transparent">
                    @error('zip')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="street" class="block text-sm font-medium text-[var(--color-text)] mb-1">Улица*</label>
                    <input type="text" id="street" wire:model.defer="street"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] focus:border-transparent">
                    @error('street')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model="invoiceRequested" class="text-[var(--color-accent)]">
                    <span class="ml-2 text-sm text-[var(--color-text)]">Желая фактура</span>
                </label>
            </div>

            <div x-data="{ showInvoice: @entangle('invoiceRequested') }" x-show="showInvoice" x-cloak class="mt-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Име на фирмата</label>
                    <input type="text" wire:model.defer="companyName"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2" />
                    @error('companyName')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">ЕИК/Булстат</label>
                    <input type="text" wire:model.defer="companyID"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2" />
                    @error('companyID')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Адрес на фирмата</label>
                    <input type="text" wire:model.defer="companyAddress"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2" />
                    @error('companyAddress')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">ДДС Номер</label>
                    <input type="text" wire:model.defer="companyTaxNumber"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2" />
                    @error('companyTaxNumber')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">МОЛ</label>
                    <input type="text" wire:model.defer="companyMol"
                        class="w-full bg-transparent border border-gray-600 text-white rounded-md px-4 py-2" />
                    @error('companyMol')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>

        </div>
    </div>

    <div class="mb-6 space-y-2">
        <p class="text-sm font-medium text-gray-700">Метод на плащане</p>

        <label class="flex items-center gap-2">
            <input type="radio" value="card" wire:model="payment_method" class="text-primary" />
            <span>Плащане с карта (Stripe)</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="radio" value="cod" wire:model="payment_method" class="text-primary" />
            <span>Плащане при получаване</span>
        </label>
    </div>

    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md text-sm mb-4" role="alert"
        x-show="$wire.payment_method" x-transition>
        <template x-if="$wire.payment_method === 'card'">
            <p>
                <strong>ВАЖНО:</strong> При заплащане с карта, Вие заплащате само стойността на поръчката без разходите
                за доставка. Разходите за доставка ще бъдат начислени допълнително и ще трябва да ги заплатите на
                куриера при получаване на пратката.
            </p>
        </template>
        <template x-if="$wire.payment_method === 'cod'">
            <p>
                <strong>ВАЖНО:</strong> При заплащане при доставка с наложен платеж, Вие заплащате както стойността на
                поръчката, така и разходите за доставка директно на куриера при получаване на пратката.
            </p>
        </template>
    </div>

    <div wire:ignore id="stripe-container" data-publishable-key="{{ config('services.stripe.key') }}"
        x-data="{
            stripe: null,
            card: null,
            loading: false,

            async pay() {
                this.loading = true;

                const { paymentMethod, error } = await this.stripe.createPaymentMethod({
                    type: 'card',
                    card: this.card,
                });

                if (error) {
                    document.getElementById('card-errors').textContent = error.message;
                    this.loading = false;
                } else {
                    window.dispatchEvent(new CustomEvent('stripeTokenReceived', {
                        detail: [paymentMethod.id]
                    }));
                }
            },

            async init() {
                const key = $el.dataset.publishableKey;

                this.stripe = await window.loadStripe(key);
                const elements = this.stripe.elements();
                this.card = elements.create('card', { hidePostalCode: true });
                this.card.mount('#card-element');

                this.card.on('change', function(event) {
                    const displayError = document.getElementById('card-errors');
                    displayError.textContent = event.error ? event.error.message : '';
                });

                window.addEventListener('orderComplete', () => {
                    this.loading = false;
                });
            }
        }" x-init="init()">


        <ul class="space-y-4">
            @foreach ($items as $item)
                <li class="flex items-center gap-4">
                    <img src="{{ asset(isset($item['image']) && file_exists(public_path('storage/' . $item['image'])) ? 'storage/' . $item['image'] : 'images/products/default.jpg') }}"
                        alt="{{ $item['name'] }}" class="w-10 h-10 object-contain" />

                    <div class="flex-1">
                        <p class="text-base font-medium text-white">{{ $item['name'] }}</p>
                        <p class="text-sm text-gray-300">Количество: {{ $item['quantity'] }}</p>
                    </div>
                    <div class="text-sm font-semibold text-[var(--color-accent)] whitespace-nowrap">
                        {{ number_format($item['price'] * $item['quantity'], 2) }} {{ $item['currency'] }}
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="flex justify-between text-lg font-semibold text-white border-t border-gray-600 pt-4">
            <span>Общо:</span>
            <span>
                {{ number_format($total, 2) }} {{ $items[0]['currency']['symbol'] ?? 'лв.' }}
            </span>

        </div>


        <div class="flex items-start gap-2 mt-6">
            <input type="checkbox" wire:model="terms_accepted" id="terms_accepted"
                class="mt-1 text-[var(--color-accent)] bg-transparent border-gray-600 rounded shadow-sm focus:ring-[var(--color-accent)]" />
            <label for="terms_accepted" class="text-sm text-[var(--color-text)] leading-relaxed">
                Съгласен съм с <a href="{{ route('terms-and-conditions.index') }}" wire:navigate target="_blank"
                    class="text-[var(--color-accent-2)] underline">
                    Общите условия за ползване
                </a>
            </label>
        </div>
        @error('terms_accepted')
            <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror

        <div x-show="$wire.payment_method === 'card'" x-cloak>
            <div id="card-element" class="mt-4 p-4 border border-gray-300 rounded-md bg-white text-black"></div>
            <div id="card-errors" class="text-sm text-red-500 mt-2" role="alert"></div>
        </div>


        <button x-data="{
            loading: false,
            init() {
                window.addEventListener('orderComplete', () => {
                    this.loading = false;
                });
            }
        }"
            @click="loading = true;
        if ($wire.payment_method === 'cod') {
            $wire.pay().then(() => loading = false);
        } else {
            pay();
        }"
            :disabled="loading"
            class="w-full mt-4 bg-[var(--color-cta)] text-white py-3 rounded-lg hover:bg-emerald-700 transition font-medium shadow cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <template x-if="loading">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4" />
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z" />
                </svg>
            </template>
            <span class="text-white"
                x-text="loading
        ? 'Моля изчакайте...'
        : ($wire.payment_method === 'cod'
            ? 'Поръчка с наложен платеж'
            : 'Плащане с карта')">
            </span>
        </button>
    </div>
