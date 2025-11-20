<footer class="bg-light text-dark font-primary border-t border-secondary/40">
    <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">

        <!-- Brand -->
        <div>
            <h2 class="text-2xl font-bold text-primary mb-4">Holistica</h2>
            <p class="text-dark/70 text-sm leading-relaxed">
                Всичко за твоята сила, енергия и възстановяване. Качество и доверие в едно място.
            </p>
        </div>

        <!-- Navigation -->
        <div>
            <h3 class="text-lg font-semibold mb-3 text-primary">Навигация</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-accent transition">Начало</a></li>
                <li><a href="{{ route('catalog') }}" class="hover:text-accent transition">Каталог</a></li>
                <li><a href="{{ route('categories') }}" class="hover:text-accent transition">Категории</a></li>
                <li><a href="{{ route('brands') }}" class="hover:text-accent transition">Марки</a></li>
                <li><a href="{{ route('blog') }}" class="hover:text-accent transition">Блог</a></li>
            </ul>
        </div>

        <!-- Legal -->
        <div>
            <h3 class="text-lg font-semibold mb-3 text-primary">Правна информация</h3>
            <ul class="space-y-2 text-sm">

                <li><a href="{{ route('terms-and-conditions.index') }}" class="hover:text-accent transition">
                        Общи условия
                    </a>
                </li>

                <li><a href="{{ route('privacy') }}" class="hover:text-accent transition">
                        Политика за поверителност
                    </a>
                </li>

                <li><a href="{{ route('cookies') }}" class="hover:text-accent transition">
                        Политика за бисквитки
                    </a>
                </li>

                <li><a href="{{ route('returns') }}" class="hover:text-accent transition">
                        Връщане и отказ
                    </a>
                </li>

                <li><a href="{{ route('shipping') }}" class="hover:text-accent transition">
                        Доставка
                    </a>
                </li>

                <li><a href="{{ route('payments') }}" class="hover:text-accent transition">
                        Плащане
                    </a>
                </li>
            </ul>
        </div>

        <!-- Contacts -->
        <div>
            <h3 class="text-lg font-semibold mb-3 text-primary">Контакти</h3>
            <ul class="space-y-2 text-sm">
                <li><span class="text-dark/70">Тел:</span>
                    <a href="tel:+359888123456" class="hover:text-accent transition">
                        +359 888 123 456
                    </a>
                </li>

                <li><span class="text-dark/70">Имейл:</span>
                    <a href="mailto:info@holistica.bg" class="hover:text-accent transition">
                        info@holistica.bg
                    </a>
                </li>

                <li><span class="text-dark/70">Адрес:</span>
                    <span class="hover:text-accent transition">гр. София, ул. __________</span>
                </li>
            </ul>
        </div>

    </div>

    <div class="border-t border-secondary/40 text-sm text-center py-4 text-dark/70">
        &copy; {{ date('Y') }} Holistica. Всички права запазени.
    </div>
</footer>
