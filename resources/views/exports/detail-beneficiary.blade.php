<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Beneficiary</title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-lg {
            font-size: 1.125rem;
        }

        .tracking-tight {
            letter-spacing: -0.015em;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .max-w-[250px],
        .lg\:max-w-[300px] {
            max-width: 250px;
        }

        .grid {
            display: grid;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grid-cols-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .gap-1 {
            gap: 0.15rem;
        }

        .my-3 {
            margin-top: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .rounded-md {
            border-radius: 0.375rem;
        }

        .object-cover {
            object-fit: cover;
        }

        .w-full {
            width: 100%;
        }

        .h-full {
            height: 100%;
        }

        .-mb-1 {
            margin-bottom: -0.25rem;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .text-neutral-600 {
            color: #4B5563;
        }

        .max-w-[150px],
        .lg\:max-w-[250px] {
            max-width: 150px;
        }

        .col-span-2 {
            grid-column: span 2 / span 2;
        }

        .my-5 {
            margin-top: 1.25rem;
            margin-bottom: 1.25rem;
        }
    </style>
</head>

<body>
    <div>
        <p class="font-semibold text-lg tracking-tight mb-2">Biodata Penerima Manfaat</p>

        <div class="max-w-[150px] lg:max-w-[200px]">
            <img src="data:image/jpeg;base64,{{ $beneficiary->photo_base64 }}" alt="Image"
                class="rounded-md object-cover" style="width: 150px; height: 200px">
        </div>

        <div class="grid grid-cols-4 gap-1 my-3">
            <div>
                <p class="-mb-1 text-sm font-semibold text-neutral-600">NIK</p>
                <p>{{ $beneficiary->nik }}</p>
            </div>
            <div>
                <p class="-mb-1 text-sm font-semibold text-neutral-600">Nama</p>
                <p>{{ $beneficiary->name }}</p>
            </div>
            <div>
                <p class="-mb-1 text-sm font-semibold text-neutral-600">Jenis Kelamin</p>
                <p>{{ $beneficiary->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
            </div>
            <div>
                <p class="-mb-1 text-sm font-semibold text-neutral-600">Tempat Lahir</p>
                <p>{{ $beneficiary->place_of_birth }}</p>
            </div>
            <div>
                <p class="-mb-1 text-sm font-semibold text-neutral-600">Tanggal Lahir</p>
                <p>{{ \Carbon\Carbon::parse($beneficiary->date_of_birth)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="-mb-1 text-sm font-semibold text-neutral-600">RT/RW</p>
                <p>{{ $beneficiary->neighborhood_unit }}</p>
            </div>
            <div>
                <p class="-mb-1 text-sm font-semibold text-neutral-600">Ukuran Baju</p>
                <p>{{ $beneficiary->shirt_size }}</p>
            </div>
            <div>
                <p class="-mb-1 text-sm font-semibold text-neutral-600">Ukuran Sepatu</p>
                <p>{{ $beneficiary->shoe_size }}</p>
            </div>
            <div>
                <p class="-mb-1 text-sm font-semibold text-neutral-600">Pendidikan Terakhir</p>
                <p>{{ $beneficiary->last_education }}, Kelas {{ $beneficiary->school_grade }}</p>
            </div>
            <div>
                <p class="-mb-1 text-sm font-semibold text-neutral-600">Status</p>
                <p>{{ $beneficiary->status }}</p>
            </div>
            <div>
                <p class="-mb-1 text-sm font-semibold text-neutral-600">No. Telp</p>
                <p>{{ $beneficiary->phone_number }}</p>
            </div>
        </div>

        <div>
            <p class="-mb-1 text-sm font-semibold text-neutral-600">Keterangan</p>
            <p>{{ $beneficiary->description }}</p>
        </div>
    </div>

    <hr class="my-5">

    <div class="grid grid-cols-2 gap-1">
        <div>
            <p class="font-semibold text-lg tracking-tight mb-2">Data Ayah</p>
            <div class="grid grid-cols-2 gap-1 my-3">
                <div class="max-w-[150px] lg:max-w-[250px] col-span-2">
                    <img src="data:image/jpeg;base64,{{ $beneficiary->father_photo_base64 }}" alt="Image"
                        class="rounded-md object-cover" style="width: 150px; height: 200px">
                </div>
                <div>
                    <p class="-mb-1 text-sm font-semibold text-neutral-600">Ayah</p>
                    <p>{{ $beneficiary->father }}</p>
                </div>
                <div>
                    <p class="-mb-1 text-sm font-semibold text-neutral-600">Akta Kematian Ayah</p>
                    <p>{{ $beneficiary->father_death_certificate_number }}</p>
                </div>
            </div>
        </div>

        <div>
            <p class="font-semibold text-lg tracking-tight mb-2">Data Ibu</p>
            <div class="grid grid-cols-2 gap-1 my-3">
                <div class="max-w-[150px] lg:max-w-[250px] col-span-2">
                    <img src="data:image/jpeg;base64,{{ $beneficiary->mother_photo_base64 }}" alt="Image"
                        class="rounded-md object-cover" style="width: 150px; height: 200px">
                </div>
                <div>
                    <p class="-mb-1 text-sm font-semibold text-neutral-600">Ibu</p>
                    <p>{{ $beneficiary->mother }}</p>
                </div>
                <div>
                    <p class="-mb-1 text-sm font-semibold text-neutral-600">Akta Kematian Ibu</p>
                    <p>{{ $beneficiary->mother_death_certificate_number }}</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
