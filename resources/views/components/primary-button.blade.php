<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'frosted-button-login inline-flex items-center px-4 py-2 text-white rounded-lg transition-all focus:outline-none']) }}>
    {{ $slot }}
</button>
