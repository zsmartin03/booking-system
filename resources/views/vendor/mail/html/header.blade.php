@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<div style="
    color: #c6d0f5;
    font-size: 20px;
    font-weight: 600;
    text-decoration: none;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    padding: 20px 0;
    font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
">
{{ $slot }}
</div>
@endif
</a>
</td>
</tr>
