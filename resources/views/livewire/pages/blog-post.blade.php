<div class="max-w-4xl mx-auto py-12 px-6">

    <div class="flex gap-3 overflow-x-auto whitespace-nowrap py-3 mb-6">
        @foreach ($post->categories as $cat)
            <span class="px-4 py-2 bg-primary/20 text-primary rounded-full text-sm font-medium shrink-0">
                {{ $cat }}
            </span>
        @endforeach
    </div>

    <h1 class="text-4xl font-bold mb-4">{{ $post->title }}</h1>

    <div class="text-sm text-gray-600 mb-6 flex gap-4">
        <span>{{ $post->created_at->format('d.m.Y') }}</span>
        <span>{{ $post->author }}</span>
        <span>{{ count($post->tags) }} тагове</span>
    </div>

    @if ($post->image)
        <img src="{{ Storage::url($post->image) }}" class="w-full max-h-[550px] object-cover rounded-xl mb-8">
    @endif


    <article class="prose prose-lg max-w-none">
        {!! $post->content !!}
    </article>

</div>
