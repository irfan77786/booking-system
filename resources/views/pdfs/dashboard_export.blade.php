<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bookings</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #1B9CCC; color: #fff; text-align: left; }
        h2 { margin-bottom: 8px; }
    </style>
</head>
<body>
    <h2>Bookings</h2>
    <table>
        <thead>
            <tr>
                <th>Confirmation</th>
                <th>Pickup Date</th>
                <th>Pickup Time</th>
                <th>Passenger</th>
                <th>Pickup</th>
                <th>Dropoff</th>
                <th>Trip Type</th>
                <th>Return Date</th>
                <th>Return Time</th>
                <th>Payment Status</th>
                <th>Total</th>
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
                <td>${{ number_format((float)$row->total_price, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

