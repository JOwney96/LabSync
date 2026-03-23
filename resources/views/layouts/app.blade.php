<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'LabSync' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f5f7fb;
            color: #222;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        h1, h2 {
            margin-top: 0;
        }
        a {
            text-decoration: none;
            color: #1d4ed8;
        }
        a:hover {
            text-decoration: underline;
        }
        .nav {
            margin-bottom: 20px;
        }
        .nav a {
            margin-right: 15px;
            font-weight: bold;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fff;
        }
        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }
        .available { background: #dcfce7; color: #166534; }
        .checkedout { background: #fee2e2; color: #991b1b; }
        .maintenance { background: #fef3c7; color: #92400e; }
        .btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 8px;
        }
        .btn:hover {
            background: #1d4ed8;
        }
        .btn-return {
            background: #059669;
        }
        .btn-return:hover {
            background: #047857;
        }
        input[type='text'] {
            padding: 8px;
            width: 220px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-right: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
        }
        .muted {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="/equipment">Equipment</a>
        <a href="/checkouts">Current Checkouts</a>
    </div>

    @yield('content')
</div>
</body>
</html>
