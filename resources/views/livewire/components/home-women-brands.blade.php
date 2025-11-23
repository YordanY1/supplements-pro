<div class="mt-20">
    <h2 class="text-3xl font-bold text-primary mb-6 text-center">
        Топ марки за жени
    </h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">

        @foreach ($brands as $brand)
            <a href="{{ route('catalog.brand', $brand['slug']) }}"
                class="block bg-secondary/20 border border-secondary/40
                      rounded-xl p-4 text-center shadow-sm hover:shadow-md
                      hover:bg-secondary/30 transition">

                @if (!empty($brand['image']))
                    <img src="{{ asset('images/brands/' . $brand['image']) }}" alt="{{ $brand['name'] }}"
                        class="mx-auto h-12 w-auto object-contain mb-3">
                @endif

                <p class="font-semibold text-dark text-sm tracking-wide">
                    {{ $brand['name'] }}
                </p>

            </a>
        @endforeach

    </div>
</div>
