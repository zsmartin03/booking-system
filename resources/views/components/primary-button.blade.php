<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'frosted-button inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-frappe-blue focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
