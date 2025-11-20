<div x-data="{ open: !localStorage.getItem('cookie_consent') }" x-show="open" x-cloak x-transition.opacity.duration.300ms
    class="fixed bottom-4 left-4 right-4 md:left-1/2 md:-translate-x-1/2 w-auto md:w-[600px]
           bg-white shadow-2xl border border-secondary/40 rounded-2xl p-6 z-[9999]">

    <h2 class="text-xl font-bold text-primary mb-3">Бисквитки</h2>

    <p class="text-dark/70 text-sm leading-relaxed mb-4">
        Използваме бисквитки, за да подобрим работата на сайта.
        Можете да научите повече в нашата
        <a href="{{ route('cookies') }}" class="text-primary underline">Политика за бисквитки</a>.
    </p>

    <div class="flex flex-col md:flex-row gap-3">
        <button
            @click="
                localStorage.setItem('cookie_consent', 'necessary');
                open = false
            "
            class="px-5 py-2 bg-secondary/20 text-dark rounded-xl font-semibold hover:bg-secondary/30 transition cursor-pointer">
            Само необходими
        </button>

        <button
            @click="
                localStorage.setItem('cookie_consent', 'all');
                open = false
            "
            class="px-5 py-2 bg-primary text-dark rounded-xl font-semibold hover:bg-accent transition cursor-pointer">
            Приемам всички
        </button>
    </div>

</div>
