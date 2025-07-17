<div>
    <div class="relative">
        <input type="text" wire:model="query" placeholder="Търси продукт..." class="w-full px-4 py-2 border rounded" />

        @if (strlen($query) >= 2)
            <ul class="absolute bg-white shadow rounded mt-1 w-full z-50">
                @forelse($results as $product)
                    <li class="px-4 py-2 border-b hover:bg-gray-100">
                        <strong>{{ $product['name'] }}</strong><br>
                        <small>{{ $product['brand_name'] }} – {{ $product['category'] }}</small>
                    </li>
                @empty
                    <li class="px-4 py-2 text-gray-500">Няма резултати</li>
                @endforelse
            </ul>
        @endif
    </div>
</div>
