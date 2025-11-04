<div class="max-w-4xl mx-auto py-20 px-4 sm:px-6 bg-light text-dark font-primary">
    <h1 class="text-4xl font-extrabold text-primary mb-10 text-center">Твоята количка</h1>

    @forelse ($cart as $item)
        <div
            class="bg-secondary/30 border border-secondary/50 rounded-xl mb-3 px-4 py-3 flex flex-col sm:flex-row sm:items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <img src="{{ asset($item['image']) }}"
                    class="w-16 h-16 object-contain bg-white p-2 rounded-lg shadow-sm border border-secondary/40" />
                <div>
                    <h2 class="text-base font-semibold text-primary leading-snug">{{ $item['name'] }}</h2>
                    <p class="text-lg font-semibold text-dark mt-1">
                        {{ number_format($item['price'], 2) }}
                        {{ is_array($item['currency']) ? $item['currency']['symbol'] ?? 'лв.' : $item['currency']->symbol ?? 'лв.' }}
                    </p>
                </div>
            </div>

            <div class="mt-3 sm:mt-0 flex items-center gap-2 sm:gap-3">
                <button wire:click="decrement({{ $item['id'] }})"
                    class="w-7 h-7 bg-primary text-dark rounded-full text-sm font-bold hover:bg-secondary transition cursor-pointer">
                    −
                </button>

                <span class="text-dark font-semibold w-6 text-center text-sm">{{ $item['quantity'] }}</span>

                <button wire:click="increment({{ $item['id'] }})"
                    class="w-7 h-7 bg-primary text-dark rounded-full text-sm font-bold hover:bg-secondary transition cursor-pointer">
                    +
                </button>

                <button wire:click="remove({{ $item['id'] }})"
                    class="ml-2 text-red-500 hover:text-red-400 transition text-lg cursor-pointer">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    @empty
        <p class="text-dark/70 text-center text-lg mt-10">Количката е празна.</p>
    @endforelse

    @if (count($cart))
        <div class="mt-12 border-t border-secondary/50 pt-6 text-right">
            <p class="text-2xl font-bold text-dark mb-4">
                Общо: <span class="text-primary">
                    {{ number_format($this->total, 2) }} {{ $items[0]['currency']['symbol'] ?? 'лв.' }}
                </span>
            </p>

            <a wire:navigate href="{{ route('checkout') }}"
                class="inline-block px-8 py-3 bg-primary text-dark font-semibold rounded-full shadow-md hover:bg-secondary transition hover:scale-105 cursor-pointer">
                Към поръчка
            </a>
        </div>
    @endif
</div>
