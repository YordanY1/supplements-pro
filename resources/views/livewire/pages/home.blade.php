<div class="bg-light min-h-screen py-16 px-6 font-primary text-dark">
    <div class="max-w-6xl mx-auto text-center">
        <h1 class="text-5xl font-extrabold mb-6 text-primary animate-scale-fade-in tracking-wide">
            SupplementsPro
        </h1>

        <p class="text-lg text-dark/70 mb-12 max-w-2xl mx-auto">
            Всичко, от което се нуждаеш за повече сила, енергия и възстановяване – в едно място.
        </p>

        <div class="rounded-2xl overflow-hidden shadow-2xl mb-16 animate-scale-fade-in">
            <img src="{{ asset('images/background.jpg') }}" alt="Banner"
                class="w-full h-[430px] object-cover object-center">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            @foreach ([['icon' => 'fa-dumbbell', 'title' => 'Протеини', 'desc' => 'Качество за твоята мускулна маса.'], ['icon' => 'fa-bolt', 'title' => 'Аминокиселини', 'desc' => 'Подпомагат възстановяването и енергията.'], ['icon' => 'fa-fire', 'title' => 'Фет бърнъри', 'desc' => 'Изгори излишните мазнини ефективно.']] as $item)
                <div
                    class="bg-secondary/30 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 animate-scale-fade-in text-left border border-secondary/40">
                    <div class="text-accent text-4xl mb-4">
                        <i class="fas {{ $item['icon'] }}"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-primary mb-2">{{ $item['title'] }}</h2>
                    <p class="text-dark/80">{{ $item['desc'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-12">
            <livewire:components.home-featured-products />
        </div>
    </div>
</div>
