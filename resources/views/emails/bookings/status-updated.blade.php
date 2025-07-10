<x-mail::message>
# Booking Status Update

Dear {{ $booking->client->name }},

Your booking with **{{ $booking->service->business->name }}** has been updated.

## Status Change:
<x-mail::panel>
From: **{{ ucfirst($previousStatus) }}**  
To: **{{ ucfirst($booking->status) }}**
</x-mail::panel>

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

@if($booking->status === 'confirmed')
Your booking has been confirmed. We look forward to seeing you!
@elseif($booking->status === 'cancelled')
Your booking has been cancelled. If you did not request this cancellation, please contact us.
@elseif($booking->status === 'completed')
Your booking has been marked as completed. Thank you for using our services!
@endif

<x-mail::button :url="route('bookings.show', $booking->id)">
View Booking
</x-mail::button>

If you have any questions, please don't hesitate to contact us.

Thank you for choosing our services!

Regards,<br>
{{ $booking->service->business->name }}
</x-mail::message>
