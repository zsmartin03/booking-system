<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'frosted-button inline-flex items-center px-4 py-2 text-white rounded-lg hover:transform hover:-translate-y-1 transition-all font-semibold focus:outline-none focus:ring-2 focus:ring-frappe-blue focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
