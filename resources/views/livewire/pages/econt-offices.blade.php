<div class="max-w-5xl mx-auto py-10 text-white">
    <h1 class="text-3xl font-bold mb-6">Офиси на Еконт в България</h1>

    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach ($offices as $office)
            <div class="bg-gray-800 p-4 rounded shadow">
                <h2 class="font-semibold text-lg">
                    {{ $office['address']['city']['name'] ?? 'Неизвестен град' }}
                </h2>
                <p class="text-sm text-gray-300 mt-1">
                    {{ $office['address']['fullAddress'] ?? 'Няма адрес' }}
                </p>
                <p class="text-xs text-gray-500 mt-2">ID: {{ $office['id'] }}</p>
            </div>
        @endforeach
    </div>
</div>
