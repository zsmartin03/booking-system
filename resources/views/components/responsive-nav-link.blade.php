@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-frappe-lavender text-start text-base font-medium text-frappe-lavender bg-frappe-surface1 focus:outline-none focus:text-frappe-lavender focus:bg-frappe-surface2 focus:border-frappe-lavender transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-frappe-text hover:text-frappe-lavender hover:bg-frappe-surface1 hover:border-frappe-lavender focus:outline-none focus:text-frappe-lavender focus:bg-frappe-surface2 focus:border-frappe-lavender transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
