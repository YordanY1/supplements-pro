<footer class="bg-primary text-text font-primary border-t border-white/10">
    <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">

        <div>
            <h2 class="text-2xl font-bold text-accent mb-4">SupplementsPro</h2>
            <p class="text-text/70 text-sm leading-relaxed">
                Всичко за твоята сила, енергия и възстановяване. Качество и доверие в едно място.
            </p>
        </div>

        <div>
            <h3 class="text-lg font-semibold mb-3 text-accent-2">Навигация</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-accent transition">Начало</a></li>
                <li><a href="{{ route('catalog') }}" class="hover:text-accent transition">Категории</a></li>
                <li><a href="#" class="hover:text-accent transition">Марки</a></li>
            </ul>
        </div>


        <div>
            <h3 class="text-lg font-semibold mb-3 text-accent-2">Контакти</h3>
            <ul class="space-y-2 text-sm">
                <li><span class="text-text/70">Тел:</span> <a href="tel:+359888123456"
                        class="hover:text-accent transition">+359 888 123 456</a></li>
                <li><span class="text-text/70">Имейл:</span> <a href="mailto:info@supplementspro.bg"
                        class="hover:text-accent transition">info@supplementspro.bg</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-lg font-semibold mb-3 text-accent-2">Последвай ни</h3>
            <ul class="flex space-x-4 mt-2 text-xl">
                <li><a href="#" class="hover:text-accent transition"><i class="fab fa-facebook"></i></a></li>
                <li><a href="#" class="hover:text-accent transition"><i class="fab fa-instagram"></i></a></li>
                <li><a href="#" class="hover:text-accent transition"><i class="fab fa-youtube"></i></a></li>
            </ul>
        </div>

    </div>

    <div class="border-t border-white/10 text-sm text-center py-4 text-text/50">
        &copy; {{ date('Y') }} SupplementsPro. Всички права запазени.
    </div>
</footer>
