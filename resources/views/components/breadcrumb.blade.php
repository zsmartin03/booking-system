@props(['items'])

<nav class="flex items-center space-x-2 text-sm text-frappe-subtext1">
    @foreach ($items as $index => $item)
        @if ($index > 0)
            <span class="text-frappe-surface2">/</span>
        @endif

        @if (isset($item['url']) && $item['url'])
            <a href="{{ $item['url'] }}" class="hover:text-frappe-lavender transition-colors duration-200 font-medium">
                {{ $item['text'] }}
            </a>
        @else
            <span class="text-frappe-text font-medium">{{ $item['text'] }}</span>
        @endif
    @endforeach
</nav>
