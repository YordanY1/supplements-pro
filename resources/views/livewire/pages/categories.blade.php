<div class="max-w-7xl mx-auto py-12 px-6">
    <h1 class="text-4xl font-bold text-primary mb-8">Категории</h1>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">

        @foreach ($categories as $cat)
            <a href="{{ route('catalog.category', $cat['slug']) }}"
                class="block bg-secondary/30 p-4 rounded-xl shadow hover:shadow-lg
                      hover:bg-primary/10 transition text-center">

                <p class="font-semibold text-dark text-sm">{{ $cat['name'] }}</p>

            </a>
        @endforeach

    </div>
</div>
