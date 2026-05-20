<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contacts</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #1B9CCC; color: #fff; text-align: left; }
        h2 { margin-bottom: 8px; }
    </style>
</head>
<body>
    <h2>Contacts</h2>
    <table>
        <thead>
            <tr>
                <th>Account #</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Type</th>
                <th>Status</th>
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

