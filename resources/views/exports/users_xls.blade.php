<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Users Export</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px; font-family: Arial, sans-serif; font-size: 12px; }
        th { background: #1B9CCC; color: #fff; text-align: left; }
        .narrow { width: 120px; }
        .idcol { width: 90px; }
        .wide { width: 220px; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th class="idcol">Account #</th>
                <th class="narrow">First Name</th>
                <th class="narrow">Last Name</th>
                <th class="wide">Email</th>
                <th class="narrow">Phone</th>
                <th class="narrow">Type</th>
                <th class="narrow">Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            @php
                $type = ucfirst($row->contact_type ?? 'Passenger');
                $verified = !is_null($row->email_verified_at) ? 'Verified' : 'Unverified';
            @endphp
            <tr>
                <td>{{ (int)$row->id }}</td>
                <td>{{ $row->first_name ?? '' }}</td>
                <td>{{ $row->last_name ?? '' }}</td>
                <td>{{ $row->email ?? '' }}</td>
                <td>{{ $row->phone ?? '' }}</td>
                <td>{{ $type }}</td>
                <td>{{ $verified }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

