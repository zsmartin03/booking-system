<x-mail::message>
# Booking Confirmation

Dear {{ $booking->client->name }},

Thank you for booking with **{{ $booking->service->business->name }}**. Your booking has been successfully created and is now **{{ ucfirst($booking->status) }}**.

## Booking Details:

<x-mail::panel>
**Service:** {{ $booking->service->name }}  
**Date:** {{ $booking->start_time->format('l, F j, Y') }}  
**Time:** {{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}  
**Employee:** {{ $booking->employee->name }}  
**Price:** ${{ number_format($booking->total_price, 2) }}
</x-mail::panel>

@if($booking->notes)
## Additional Notes:
{{ $booking->notes }}
@endif

@if($booking->status === 'pending')
Your booking is currently pending confirmation. You will receive another email once your booking has been confirmed.
@endif

@if($booking->service->business->settings->where('key', 'cancellation_policy')->first())
## Cancellation Policy:
{{ $booking->service->business->settings->where('key', 'cancellation_policy')->first()->value }}
@endif

<x-mail::button :url="route('bookings.show', $booking->id)">
View Booking
</x-mail::button>

If you have any questions or need to make changes to your booking, please don't hesitate to contact us.

Thank you for choosing our services!

Regards,<br>
{{ $booking->service->business->name }}
</x-mail::message>
