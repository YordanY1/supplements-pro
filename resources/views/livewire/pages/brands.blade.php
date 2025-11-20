<div class="max-w-7xl mx-auto py-12 px-6">
    <h1 class="text-4xl font-bold text-primary mb-8">Марки</h1>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">

        @foreach ($brands as $brand)
            <a href="{{ route('catalog.brand', $brand['slug']) }}"
                class="block p-4 rounded-xl bg-secondary/20 hover:bg-primary/10
                       border border-secondary/40 shadow-sm hover:shadow-md
                       transition text-center">

                <p class="font-semibold text-dark text-sm tracking-wide">
                    {{ $brand['name'] }}
                </p>

            </a>
        @endforeach

    </div>
</div>
