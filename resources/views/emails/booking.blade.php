<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking {{ $isAdmin ? 'Notification' : 'Confirmation' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Abel&display=swap" rel="stylesheet">
</head>

<body
    style="font-family: 'Abel', 'Helvetica', 'Arial', sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">
    <div class="container" style="max-width: 600px; margin: 0 auto; padding: 6px; background-color: #ffffff;">
        <div class="header"
            style="padding: 20px 10px; text-align: center; border-bottom: 1px solid #eee; background-color: #ffffff;">
            <div style="background-color: #ffffff; padding: 15px; display: inline-block;">
                <img src="https://www.dallasblacklimoservice.com/img/dallas-black-limo-service-logo.png"
                    alt="Dallas Black Limo Service"
                    style="max-width: 250px; margin-bottom: 10px; height: auto; display: block; background-color: #ffffff;">
            </div>
            <h2 style="margin: 0; font-size: 22px; color: #12143e;">Booking {{ $isAdmin ? 'Notification' :
                'Confirmation' }}</h2>
            <p style="margin: 5px 0 0; font-size: 15px; color: #555;">{{ $isAdmin ? 'New booking received' : 'Your
                reservation has been confirmed!' }}</p>
        </div>

        <div class="content" style="padding: 10px 4px 20px;">
            {{-- Greeting / Intro --}}
            @if($isAdmin)
            <p style="font-size: 12px; margin: 0 0 10px;"><b>Dear Admin,</b></p>
            <p style="font-size: 12px; margin: 0 0 10px;">A new booking has been received. Please find the details
                below:</p>
            <div class="admin-note"
                style="background-color: #baddfc; border-left: 4px solid #12143e; padding: 12px; margin: 15px 0; font-size: 15px; color: #333;">
                <strong>Action Required:</strong> Please review and confirm this booking at your earliest convenience.
            </div>
            @elseif($sendToBooker)
            <p style="font-size: 12px; margin: 0 0 10px;"><b>Dear {{ $bookingData['booker_first_name'] . ' ' .
                    $bookingData['booker_last_name'] ?? 'Booker' }},</b></p>
            <p style="font-size: 12px; margin: 0 0 10px;">Thank you for booking on behalf of {{
                $bookingData['passenger_name'] ?? 'the passenger' }}. Your booking has been successfully confirmed.
                Below are the booking details:</p>
            @else
            <p style="font-size: 12px; margin: 0 0 10px;"><b>Dear {{ $bookingData['passenger_name'] ?? 'Valued Customer'
                    }},</b></p>
            <p style="font-size: 12px; margin: 0 0 10px;">Thank you for choosing our service. Your booking has been
                successfully confirmed. Below are your booking details:</p>
            @endif

            {{-- FIFA World Cup 2026 notice (matches PDF policy; full terms in attached PDF) --}}
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                style="margin: 16px 0 20px; background-color: #FDFAF3; border: 1px solid #9C844C; border-radius: 12px;">
                <tr>
                    <td style="padding: 16px; vertical-align: top;">
                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="width: 40px; vertical-align: top; padding-top: 2px;">
                                    <div
                                        style="width: 28px; height: 28px; line-height: 28px; text-align: center; border-radius: 50%; background-color: #9C844C; color: #ffffff; font-size: 16px; font-weight: 700; font-family: Georgia, 'Times New Roman', serif;">
                                        i
                                    </div>
                                </td>
                                <td style="vertical-align: top; padding-left: 8px;">
                                    <p
                                        style="margin: 0 0 10px; font-size: 13px; color: #333; font-weight: 700; line-height: 1.45;">
                                        Important Event Notice – FIFA World Cup 2026 (June 13 – July 15, 2026):
                                    </p>
                                    <p
                                        style="margin: 0; font-size: 12px; color: #333; font-style: italic; line-height: 1.5;">
                                        If this booking falls within the FIFA World Cup 2026 event dates, all rates, fees, and
                                        minimums are subject to change without notice based on event demand, availability, and
                                        operational conditions. You acknowledge and agree that these rates are not guaranteed
                                        until final payment is made in accordance with our policy. By confirming this
                                        reservation, you expressly authorize any rate adjustments and agree that such
                                        changes are not a basis for refund, credit, cancellation, or chargeback.
                                    </p>
                                    <p style="margin: 10px 0 0; font-size: 11px; color: #555; line-height: 1.4;">
                                        Complete FIFA 2026 (Dallas–Fort Worth) event terms are included in your attached
                                        booking PDF.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- Booking confirmation fields (aligned with resources/views/pdfs/booking.blade.php) --}}
            <div class="booking-details"
                style="background-color: #f8f9fa; border-radius: 4px; margin: 20px 0; border: 1px solid #e0e0e0;">
                <h3
                    style="background: #12143e; margin: 0; padding: 8px 12px; font-size: 14px; color: #ffffff; border-radius: 4px 4px 0 0;">
                    Booking Confirmation #{{ $bookingData['booking_id'] ?? 'N/A' }}</h3>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-size: 12px; padding: 10px;">
                    <tr>
                        <td colspan="2" style="text-align: right; color: #555; padding: 4px 0 8px; font-size: 11px;">
                            <strong>Last Modified On:</strong> {{ now()->format('m/d/Y h:i A') }}
                        </td>
                    </tr>
                    @if(!empty($bookingData['pickup_date']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Pick-up Date:</td>
                        <td style="color: #333; padding: 4px 0;">{{ \Carbon\Carbon::parse($bookingData['pickup_date'])->format('m/d/Y - l') }}</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['pickup_time']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Pick-up Time:</td>
                        <td style="color: #333; padding: 4px 0;">{{ \Carbon\Carbon::parse($bookingData['pickup_time'])->format('h:i A') }}</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['return_date']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Return Date:</td>
                        <td style="color: #333; padding: 4px 0;">{{ \Carbon\Carbon::parse($bookingData['return_date'])->format('m/d/Y - l') }}</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['return_time']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Return Time:</td>
                        <td style="color: #333; padding: 4px 0;">{{ \Carbon\Carbon::parse($bookingData['return_time'])->format('h:i A') }}</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['hours']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Hours:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $bookingData['hours'] }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Service Type:</td>
                        <td style="color: #333; padding: 4px 0;">{{ !empty($bookingData['hours']) ? 'Hourly/As Directed' : 'To Airport' }}</td>
                    </tr>
                    @if(!empty($bookingData['passenger_name']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Passenger:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $bookingData['passenger_name'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['booking_id']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Client Ref#:</td>
                        <td style="color: #333; padding: 4px 0;">N/A</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['phone']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Phone Number:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $bookingData['phone'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['passengers']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">No. of Pass:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $bookingData['passengers'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['vehicle_type']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Vehicle Type:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $bookingData['vehicle_type'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['booker_first_name']) || !empty($bookingData['booker_last_name']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Primary/Billing Contact:</td>
                        <td style="color: #333; padding: 4px 0;">{{ trim(($bookingData['booker_first_name'] ?? '') . ' ' . ($bookingData['booker_last_name'] ?? '')) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Passenger Email:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $bookingData['email'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Payment Method:</td>
                        <td style="color: #333; padding: 4px 0;">Credit Card</td>
                    </tr>
                </table>
            </div>

            {{-- Booker Information (order/labels match PDF; placeholder omitted on booker-only copy) --}}
            @if(!empty($bookingData['isBookingForOthers']) && (!empty($bookingData['booker_first_name']) || !empty($bookingData['booker_last_name']) || !empty($bookingData['booker_email']) || !empty($bookingData['booker_number'])))
            <div class="booking-details"
                style="background-color: #f8f9fa; border-radius: 4px; margin: 20px 0; border: 1px solid #e0e0e0;">
                <h3
                    style="background: #baddfc; margin: 0; padding: 8px 12px; font-size: 14px; color: #12143e; border-radius: 4px 4px 0 0;">
                    Booker Information</h3>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-size: 12px; padding: 10px;">
                    @if(!empty($bookingData['booker_first_name']) || !empty($bookingData['booker_last_name']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Booker Name:</td>
                        <td style="color: #333; padding: 4px 0;">{{ trim(($bookingData['booker_first_name'] ?? '') . ' ' . ($bookingData['booker_last_name'] ?? '')) }}</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['booker_email']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Booker Email:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $bookingData['booker_email'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['booker_number']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Booker Phone:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $bookingData['booker_number'] }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            @elseif(!$sendToBooker)
            <div class="booking-details"
                style="background-color: #f8f9fa; border-radius: 4px; margin: 20px 0; border: 1px solid #e0e0e0;">
                <h3
                    style="background: #baddfc; margin: 0; padding: 8px 12px; font-size: 14px; color: #12143e; border-radius: 4px 4px 0 0;">
                    Booker Information</h3>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-size: 12px; padding: 10px;">
                    <tr>
                        <td style="color: #666; padding: 4px 0;">****** Information not provided ******</td>
                    </tr>
                </table>
            </div>
            @endif

            {{-- Trip Routing (matches PDF labels; hourly shows stop line) --}}
            <div class="booking-details"
                style="background-color: #f8f9fa; border-radius: 4px; margin: 20px 0; border: 1px solid #e0e0e0;">
                <h3
                    style="background: #baddfc; margin: 0; padding: 8px 12px; font-size: 14px; color: #12143e; border-radius: 4px 4px 0 0;">
                    Trip Routing Information</h3>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-size: 12px; padding: 10px;">
                    @if(!empty($bookingData['pickup_location']) || !empty($bookingData['dropoff_location']) || !empty($bookingData['hours']))
                    @if(!empty($bookingData['pickup_location']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Pick-up Location:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $bookingData['pickup_location'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['hours']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Stop Location:</td>
                        <td style="color: #333; padding: 4px 0;">STOP AS DIRECTED</td>
                    </tr>
                    @endif
                    @if(!empty($bookingData['dropoff_location']))
                    <tr>
                        <td style="font-weight: bold; color: #666; padding: 4px 0;">Drop-off Location:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $bookingData['dropoff_location'] }}</td>
                    </tr>
                    @endif
                    @else
                    <tr>
                        <td style="color: #666; padding: 4px 0;">****** Information not provided ******</td>
                    </tr>
                    @endif
                </table>
            </div>

            {{-- Flight / Airport (aligned with PDF section) --}}
            @php
                $fd = $bookingData['flight_details'] ?? null;
                $hasFlightBlock = is_array($fd)
                    && !empty($fd['flight_number'])
                    && !empty($fd['pickup_flight_details']);
            @endphp
            <div class="booking-details"
                style="background-color: #f8f9fa; border-radius: 4px; margin: 20px 0; border: 1px solid #e0e0e0;">
                <h3
                    style="background: #12143e; margin: 0; padding: 8px 12px; font-size: 14px; color: #ffffff; border-radius: 4px 4px 0 0;">
                    Flight/Airport Information</h3>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-size: 12px; padding: 10px;">
                    @if($hasFlightBlock)
                    @if(!empty($fd['flight_number']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Flight Number:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $fd['flight_number'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($fd['pickup_flight_details']))
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Pickup Flight Details:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $fd['pickup_flight_details'] }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Meet Option:</td>
                        <td style="color: #333; padding: 4px 0;">{{ $fd['meet_option'] ?? 'Not Specified!' }}</td>
                    </tr>
                    @else
                    <tr>
                        <td style="color: #666; padding: 4px 0;">****** Information not provided ******</td>
                    </tr>
                    @endif
                </table>
            </div>

            {{-- Notes (PDF Notes/Comments) --}}
            <div class="booking-details"
                style="background-color: #f8f9fa; border-radius: 4px; margin: 20px 0; border: 1px solid #e0e0e0;">
                <h3
                    style="background: #12143e; margin: 0; padding: 8px 12px; font-size: 14px; color: #ffffff; border-radius: 4px 4px 0 0;">
                    Notes/Comments</h3>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-size: 12px; padding: 10px;">
                    <tr>
                        <td style="color: #333; padding: 4px 0;">
                            ****** {{ !empty($bookingData['special_instructions']) ? $bookingData['special_instructions'] : 'Information not provided' }} ******
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Charges & Fees (matches PDF breakdown) --}}
            @if(isset($bookingData['total_amount']))
            <div class="booking-details"
                style="background-color: #f8f9fa; border-radius: 4px; margin: 20px 0; border: 1px solid #e0e0e0;">
                <h3
                    style="background: #baddfc; margin: 0; padding: 8px 12px; font-size: 14px; color: #12143e; border-radius: 4px 4px 0 0;">
                    Charges &amp; Fees</h3>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-size: 12px; padding: 10px;">
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Fare (All inclusive):</td>
                        <td style="color: #333; padding: 4px 0;"><strong>${{ number_format($bookingData['total_amount'], 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #666; width: 40%; padding: 4px 0;">Other charges:</td>
                        <td style="color: #333; padding: 4px 0;"><strong>$0.00</strong></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #28a745; width: 40%; padding: 4px 0;">Payment/Deposits:</td>
                        <td style="color: #333; padding: 4px 0;"><strong>$0.00</strong></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #dc3545; width: 40%; padding: 4px 0;">Total Due:</td>
                        <td style="color: #dc3545; padding: 4px 0;"><strong>${{ number_format($bookingData['total_amount'], 2) }}</strong></td>
                    </tr>
                </table>
            </div>
            @else
            <div class="booking-details"
                style="background-color: #f8f9fa; border-radius: 4px; margin: 20px 0; border: 1px solid #e0e0e0;">
                <h3
                    style="background: #baddfc; margin: 0; padding: 8px 12px; font-size: 14px; color: #12143e; border-radius: 4px 4px 0 0;">
                    Charges &amp; Fees</h3>
                <table cellpadding="0" cellspacing="0" width="100%" style="font-size: 12px; padding: 10px;">
                    <tr>
                        <td style="color: #666; padding: 4px 0;">****** Information not provided ******</td>
                    </tr>
                </table>
            </div>
            @endif

            <p style="font-size: 12px; margin: 20px 0 10px;"><b>Best regards,<br>{{ config('app.name') }} Team</b></p>
        </div>

        <div class="footer"
            style="text-align: center; padding: 20px 10px; font-size: 13px; color: #777; border-top: 1px solid #e1e1e1;">
            <p style="margin: 0 0 5px;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>