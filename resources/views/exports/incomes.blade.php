<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Submit Date</th>
                <th scope="col">Transfer Date</th>
                <th scope="col">Ambassador</th>
                <th scope="col">Donor</th>
                <th scope="col">Payment Method</th>
                <th scope="col">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($incomes as $income)
            <tr>
                <th scope="row">{{ $income->id }}</th>
                <td>{{ $income->created_at }}</td>
                <td>{{ $income->transfer_date }}</td>
                <td>{{ $income->ambassador->name }}</td>
                <td>{{ $income->donor }}</td>
                <td>{{ $income->payment_method }}</td>
                <td>{{ $income->amount }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>