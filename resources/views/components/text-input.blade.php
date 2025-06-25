@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'bg-frappe-surface0/60 border-frappe-surface1/30 text-frappe-text focus:border-frappe-blue focus:ring-frappe-blue/50 rounded-md shadow-sm backdrop-blur-sm']) }}>
