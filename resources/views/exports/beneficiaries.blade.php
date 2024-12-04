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
        <h2>Penerima Manfaat</h2>
    </div>

    <div class="report-info">
        <p class="mb-1"><strong>Tgl. Dibuat:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        <p class="mb-1"><strong>Total Data:</strong> {{ count($beneficiaries) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>NIK</th>
                <th>Tmpt Lahir</th>
                <th>Tgl. Lahir</th>
                <th>Nama</th>
                <th>RT/RW</th>
                <th>JK</th>
                <th>Pend. Terakhir</th>
                {{-- <th>Foto</th> --}}
                <th>Ayah</th>
                {{-- <th>Foto Ayah</th> --}}
                <th>Ibu</th>
                {{-- <th>Foto Ibu</th> --}}
                {{-- <th>Ukuran Baju</th> --}}
                {{-- <th>Ukuran Sepatu</th> --}}
                {{-- <th>No. Akta Kematian Ayah</th> --}}
                {{-- <th>No. Akta Kematian Ibu</th> --}}
                <th>No. Telp</th>
                <th>Status</th>
                {{-- <th>Deskripsi</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($beneficiaries as $beneficiary)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $beneficiary->nik }}</td>
                    <td>{{ $beneficiary->place_of_birth }}</td>
                    <td>{{ \Carbon\Carbon::parse($beneficiary->date_of_birth)->format('d/m/Y') }}</td>
                    <td>{{ $beneficiary->name }}</td>
                    <td>{{ $beneficiary->neighborhood_unit }}</td>
                    <td>{{ $beneficiary->gender }}</td>
                    <td>{{ $beneficiary->last_education }}, Kelas {{ $beneficiary->school_grade }}</td>
                    {{-- <td><img src="{{ $beneficiary->photo }}" alt="Foto" width="50"></td> --}}
                    <td>{{ $beneficiary->father }}</td>
                    {{-- <td><img src="{{ $beneficiary->father_photo }}" alt="Foto Ayah" width="50"></td> --}}
                    <td>{{ $beneficiary->mother }}</td>
                    {{-- <td><img src="{{ $beneficiary->mother_photo }}" alt="Foto Ibu" width="50"></td> --}}
                    {{-- <td>{{ $beneficiary->shirt_size }}</td> --}}
                    {{-- <td>{{ $beneficiary->shoe_size }}</td> --}}
                    {{-- <td>{{ $beneficiary->father_death_certificate_number }}</td> --}}
                    {{-- <td>{{ $beneficiary->mother_death_certificate_number }}</td> --}}
                    <td>{{ $beneficiary->phone_number }}</td>
                    <td>{{ $beneficiary->status }}</td>
                    {{-- <td>{{ $beneficiary->description }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Ini adalah laporan data penerima manfaat.</p>
    </div>
</body>

</html>
