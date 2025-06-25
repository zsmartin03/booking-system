<button
    {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-frappe-surface0/30 to-frappe-surface1/30 border border-frappe-surface1/30 rounded-md font-semibold text-xs text-frappe-text uppercase tracking-widest shadow-sm hover:from-frappe-surface0/50 hover:to-frappe-surface1/50 focus:outline-none focus:ring-2 focus:ring-frappe-blue focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 backdrop-blur-sm']) }}>
    {{ $slot }}
</button>
