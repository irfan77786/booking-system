<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bookings Export</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px; font-family: Arial, sans-serif; font-size: 12px; }
        th { background: #1B9CCC; color: #fff; text-align: left; }
        .money { mso-number-format:"\0024#,##0.00"; }
        .narrow { width: 90px; }
        .wide { width: 220px; }
    </style>
    </head>
<body>
    <table>
        <thead>
            <tr>
                <th class="narrow">Confirmation</th>
                <th class="narrow">Pickup Date</th>
                <th class="narrow">Pickup Time</th>
                <th class="wide">Passenger</th>
                <th class="wide">Pickup</th>
                <th class="wide">Dropoff</th>
                <th class="narrow">Trip Type</th>
                <th class="narrow">Return Date</th>
                <th class="narrow">Return Time</th>
                <th class="narrow">Payment Status</th>
                <th class="narrow">Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            @php
                $passenger = $row->booker ? trim(($row->booker->first_name ?? '').' '.($row->booker->last_name ?? '')) : '';
                $tripType = $row->round_trip == 1 ? 'Round Trip' : 'One-way';
                $returnDate = '';
                $returnTime = '';
                if ($row->round_trip == 1) {
                    $returnDate = $row->returnService->pickup_date ?? $row->return_date ?? '';
                    $returnTime = $row->returnService->pickup_time ?? $row->return_time ?? '';
                }
            @endphp
            <tr>
                <td>{{ $row->booking_id }}</td>
                <td>{{ $row->pickup_date }}</td>
                <td>{{ $row->pickup_time }}</td>
                <td>{{ $passenger ?: '-' }}</td>
                <td>{{ $row->pickup_location }}</td>
                <td>{{ $row->dropoff_location }}</td>
                <td>{{ $tripType }}</td>
                <td>{{ $returnDate }}</td>
                <td>{{ $returnTime }}</td>
                <td>{{ ucfirst(strtolower($row->payment_status)) }}</td>
                <td class="money">{{ number_format((float)$row->total_price, 2, '.', '') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

