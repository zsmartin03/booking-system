@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-frappe-subtext1']) }}>
    {{ $value ?? $slot }}
</label>
