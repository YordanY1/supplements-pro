<div class="max-w-6xl mx-auto px-4 py-12 bg-gray-50 min-h-screen">

    <h1 class="text-3xl font-extrabold text-center text-gray-900 tracking-tight mb-10">
        Финализиране на поръчката
    </h1>

    <div class="grid lg:grid-cols-2 gap-10">

        {{-- LEFT — USER INFO --}}
        <div class="bg-white/90 backdrop-blur-xl p-8 rounded-2xl shadow-xl space-y-8 border border-gray-100">

            <h2 class="text-xl font-bold text-gray-900">Вашите данни</h2>

            {{-- PERSONAL INFO --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Име *</label>
                    <input type="text" wire:model.defer="first_name" class="form-input" placeholder="Иван">
                    @error('first_name')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Фамилия *</label>
                    <input type="text" wire:model.defer="last_name" class="form-input" placeholder="Иванов">
                    @error('last_name')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Имейл *</label>
                    <input type="email" wire:model.defer="email" class="form-input" placeholder="email@domain.com">
                    @error('email')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-gray-700">Телефон *</label>
                    <input type="text" wire:model.defer="phone" class="form-input" placeholder="0888 123 456">
                    @error('phone')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- SHIPPING METHOD --}}
            <div class="space-y-2 pt-2">
                <p class="text-sm font-semibold text-gray-700">Метод на доставка</p>

                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input type="radio" wire:model.live="shipping_method" value="econt_office" class="text-primary">
                    <span>До офис на Еконт</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input type="radio" wire:model.live="shipping_method" value="address" class="text-primary">
                    <span>До адрес</span>
                </label>
            </div>

            @php
                $cityOptions = $cityOptions ?? [];
                $streetOptions = $streetOptions ?? [];
                $officeOptions = $officeOptions ?? [];
            @endphp

            {{-- ADDRESS MODE --}}
            @if ($shipping_method === 'address')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-2">

                    {{-- City --}}
                    <div class="space-y-1" x-data>
                        <label class="text-sm font-medium text-gray-700">Град *</label>
                        <input type="text" wire:model.live.debounce.300ms="citySearch" class="form-input"
                            placeholder="Започни да пишеш..." autocomplete="off">

                        @error('cityId')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror

                        @if (!empty($cityOptions))
                            <ul class="bg-white border shadow rounded mt-1 max-h-48 overflow-auto relative z-50"
                                x-on:click.outside="$wire.set('cityOptions', [])">
                                @foreach ($cityOptions as $opt)
                                    <li class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                                        wire:click="selectCity({{ $opt['id'] }}, '{{ $opt['label'] }}', '{{ $opt['post_code'] ?? '' }}')">
                                        {{ $opt['label'] }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Street --}}
                    <div class="space-y-1" x-data>
                        <label class="text-sm font-medium text-gray-700">Улица *</label>
                        <input type="text" wire:model.live.debounce.300ms="streetSearch" class="form-input"
                            placeholder="ул. Иван Вазов" autocomplete="off">

                        @error('streetCode')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror

                        @if (!empty($streetOptions))
                            <ul class="bg-white border shadow rounded mt-1 max-h-48 overflow-auto relative z-50"
                                x-on:click.outside="$wire.set('streetOptions', [])">
                                @foreach ($streetOptions as $opt)
                                    <li class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                                        wire:click="selectStreet({{ $opt['id'] }}, {{ $opt['value'] }}, '{{ $opt['label'] }}')">
                                        {{ $opt['label'] }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Street Num --}}
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">Номер *</label>
                        <input type="text" wire:model.live="streetNum" class="form-input" placeholder="№">
                    </div>
                </div>
            @endif

            {{-- OFFICE MODE --}}
            @if ($shipping_method === 'econt_office')
                <div class="mt-2 space-y-1" x-data>
                    <label class="text-sm font-medium text-gray-700">Офис на Еконт *</label>
                    <input type="text" wire:model.live.debounce.300ms="officeSearch" class="form-input"
                        placeholder="Търси офис..." autocomplete="off">

                    @error('officeCode')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror

                    @if (!empty($officeOptions))
                        <ul class="bg-white border shadow rounded mt-1 max-h-64 overflow-auto relative z-50"
                            x-on:click.outside="$wire.set('officeOptions', [])">
                            @foreach ($officeOptions as $opt)
                                <li class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                                    wire:click="selectOffice('{{ $opt['value'] }}', '{{ $opt['label'] }}')">
                                    {{ $opt['label'] }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif


            {{-- INVOICE --}}
            <label class="flex items-center gap-2 text-sm font-medium cursor-pointer pt-3">
                <input type="checkbox" wire:model="invoiceRequested" class="rounded border-gray-300">
                <span>Желая фактура</span>
            </label>

            <div x-data="{ open: @entangle('invoiceRequested') }" x-show="open" class="space-y-3">
                @foreach ([['companyName', 'Име на фирма'], ['companyID', 'ЕИК/Булстат'], ['companyAddress', 'Адрес'], ['companyTaxNumber', 'ДДС номер'], ['companyMol', 'МОЛ']] as [$model, $label])
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
                        <input type="text" wire:model.defer="{{ $model }}" class="form-input">
                    </div>
                @endforeach
            </div>

            {{-- Payment --}}
            <div class="space-y-2 pt-4">
                <p class="text-sm font-semibold text-gray-700">Метод на плащане</p>

                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="radio" wire:model="payment_method" value="card" class="text-primary">
                    <span>Карта (Stripe)</span>
                </label>

                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="radio" wire:model="payment_method" value="cod" class="text-primary">
                    <span>Наложен платеж</span>
                </label>
            </div>

            {{-- Terms --}}
            <div class="flex gap-2 text-sm">
                <input type="checkbox" wire:model="terms_accepted" class="rounded border-gray-300">
                <label>Съгласен съм с <a class="text-primary underline"
                        href="{{ route('terms-and-conditions.index') }}" target="_blank">общите условия</a></label>
            </div>
        </div>

        {{-- RIGHT — ORDER SUMMARY --}}
        <div class="bg-white/90 backdrop-blur-xl p-8 rounded-2xl shadow-xl border border-gray-100 space-y-6">

            <h2 class="text-xl font-bold text-gray-900">Вашата поръчка</h2>

            <ul class="space-y-4">
                @foreach ($items as $item)
                    <li class="flex items-center gap-4">
                        <img src="{{ $item['image'] }}"
                            class="w-16 h-16 rounded-lg object-contain border bg-gray-50">
                        <div class="flex-1">
                            <p class="font-medium">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-500">Количество: {{ $item['quantity'] }}</p>
                        </div>
                        <p class="font-semibold text-gray-900">
                            {{ number_format($item['price'] * $item['quantity'], 2) }} лв</p>
                    </li>
                @endforeach
            </ul>

            <div class="border-t pt-3 text-gray-700 space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Доставка:</span>
                    <span>{{ number_format($shippingCost, 2) }} лв</span>
                </div>
                <div class="flex justify-between font-bold text-lg text-gray-900">
                    <span>Общо:</span>
                    <span>{{ number_format($totalWithShipping, 2) }} лв</span>
                </div>
            </div>

            <button wire:click="submitOrder" wire:loading.attr="disabled"
                class="w-full py-3 rounded-xl bg-primary text-white font-bold">
                <span wire:loading.remove>Потвърди поръчката</span>
                <span wire:loading>Обработваме…</span>
            </button>
        </div>
    </div>
</div>
