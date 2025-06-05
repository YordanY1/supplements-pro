<div x-data="{ animate: false }" x-init="$watch('animate', value => {
    if (value) setTimeout(() => animate = false, 300);
})" @cart-updated.window="animate = true">
    <a wire:navigate href="{{ route('cart') }}" class="relative text-accent hover:text-accent-2 transition">
        <i class="fas fa-shopping-cart text-2xl"></i>

        @if ($count > 0)
            <span :class="{ 'animate-bounce': animate }"
                class="absolute -top-2 -right-3 bg-red-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center shadow-md transition">
                {{ $count }}
            </span>
        @endif
    </a>
</div>
