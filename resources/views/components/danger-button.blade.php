<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-frappe-red/80 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-frappe-red hover:transform hover:-translate-y-1 active:bg-frappe-red/90 focus:outline-none focus:ring-2 focus:ring-frappe-red focus:ring-offset-2 transition ease-in-out duration-150 backdrop-blur-sm']) }}>
    {{ $slot }}
</button>
