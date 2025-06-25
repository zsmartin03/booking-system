<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'frosted-button-save inline-flex items-center px-4 py-2 text-white rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-frappe-green focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
