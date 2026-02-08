<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
    </head>
<body>
<h1>{{ $title }}</h1>
<table>
    <thead>
    <tr>
        @foreach ($columns as $col)
            <th>{{ $col }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $r)
        <tr>
            @foreach ($columns as $col)
                <td>{{ $r[$col] ?? '' }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
