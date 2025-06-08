<div class="max-w-4xl mx-auto py-20 px-4 sm:px-6">
    <h1 class="text-4xl font-extrabold text-accent mb-10 text-center">Твоята количка</h1>

    @forelse ($cart as $item)
        <div
            class="bg-card border border-white/10 rounded-xl mb-3 px-4 py-3 flex flex-col sm:flex-row sm:items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <img src="{{ asset($item['image']) }}"
                    class="w-16 h-16 object-contain bg-white p-2 rounded-lg shadow-sm" />
                <div>
                    <h2 class="text-base font-semibold text-accent leading-snug">{{ $item['name'] }}</h2>
                    <p class="text-xl font-semibold text-white mb-4">
                        {{ number_format($item['price'], 2) }}
                        {{ is_array($item['currency']) ? $item['currency']['symbol'] ?? 'лв.' : $item['currency']->symbol ?? 'лв.' }}
                    </p>
                </div>
            </div>

            <div class="mt-3 sm:mt-0 flex items-center gap-2 sm:gap-3">
                <button wire:click="decrement({{ $item['id'] }})"
                    class="w-7 h-7 bg-accent text-white rounded-full text-sm font-bold hover:bg-accent-2 transition cursor-pointer">
                    −
                </button>

                <span class="text-white font-semibold w-6 text-center text-sm">{{ $item['quantity'] }}</span>

                <button wire:click="increment({{ $item['id'] }})"
                    class="w-7 h-7 bg-accent text-white rounded-full text-sm font-bold hover:bg-accent-2 transition cursor-pointer">
                    +
                </button>

                <button wire:click="remove({{ $item['id'] }})"
                    class="ml-2 text-red-500 hover:text-red-400 transition text-lg cursor-pointer">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    @empty
        <p class="text-white text-center text-lg mt-10">Количката е празна.</p>
    @endforelse

    @if (count($cart))
        <div class="mt-12 border-t border-white/10 pt-6 text-right">
            <p class="text-2xl font-bold text-white mb-4">
                Общо: <span class="text-accent">
                    {{ number_format($this->total, 2) }} {{ $items[0]['currency']['symbol'] ?? 'лв.' }}
                </span>
            </p>

            <a wire:navigate href="{{ route('checkout') }}"
                class="inline-block px-8 py-3 bg-cta text-white font-bold rounded-full shadow hover:bg-accent transition hover:scale-105 cursor-pointer">
                Към поръчка
            </a>
        </div>
    @endif
</div>
