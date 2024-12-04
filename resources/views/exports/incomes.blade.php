<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <style>
        @page {
            margin: 1.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            max-width: 150px;
            height: auto;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .report-info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #2c3e50;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            text-align: center;
            font-size: 0.8em;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>

<body class="p-2">
    <div class="header">
        <h2>Pendapatan</h2>
    </div>

    <div class="report-info">
        <p class="mb-1"><strong>Tgl. Dibuat:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        <p class="mb-1"><strong>Total Data:</strong> {{ count($incomes) }}</p>
        @php
            $totalAmount = 0;
            foreach ($incomes as $income) {
                $totalAmount += $income->amount;
            }
        @endphp
        <p><strong>Total Pendapatan:</strong> Rp{{ number_format($totalAmount, 0, ',', '.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tgl. Submit</th>
                <th>Tgl. Transfer</th>
                <th>Duta</th>
                <th>Donatur</th>
                <th>Metode</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($incomes as $income)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($income->created_at)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($income->transfer_date)->format('d/m/Y') }}</td>
                    <td>{{ $income->ambassador->name }}</td>
                    <td>{{ $income->donor }}</td>
                    <td>{{ $income->payment_method }}</td>
                    <td>{{ $income->amount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Ini adalah laporan data pendapatan.</p>
    </div>
</body>

</html>
