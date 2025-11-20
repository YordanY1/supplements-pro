<div class="max-w-6xl mx-auto py-12 px-6">
    <h1 class="text-4xl font-bold mb-8">Блог</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach ($posts as $post)
            <a href="{{ route('blog.post', $post->slug) }}"
                class="block bg-white rounded-xl shadow hover:shadow-lg transition">

                <img src="{{ Storage::url($post->image) }}" class="w-full h-48 object-cover rounded-t-xl">


                <div class="p-4">
                    <h2 class="text-lg font-semibold mb-2">{{ $post->title }}</h2>

                    <p class="text-gray-600 text-sm mb-2">
                        {{ $post->excerpt }}
                    </p>

                    <div class="text-xs text-gray-500">
                        {{ $post->created_at->format('d.m.Y') }} • {{ $post->author }}
                    </div>
                </div>

            </a>
        @endforeach
    </div>

    <div class="mt-12">
        {{ $posts->links() }}
    </div>
</div>
