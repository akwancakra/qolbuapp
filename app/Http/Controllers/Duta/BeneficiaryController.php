<?php

namespace App\Http\Controllers\Duta;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\UpdateBeneficiaryRequest;
use Carbon\Carbon;
use Inertia\Inertia;

class BeneficiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $beneficiaries = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'nickname' => 'Johnny',
                'birthdate' => '2000-01-01',
                'address' => '123 Main St',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '12345',
                'phone' => '081234567890',
                'email' => 'john@example.com',
                'parent_mother' => 'Jane Doe',
                'parent_mother_phone' => '081234567891',
                'parent_father' => 'Jack Doe',
                'parent_father_phone' => '081234567892',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'nickname' => 'Janie',
                'birthdate' => '2001-02-02',
                'address' => '456 Elm St',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'country' => 'Indonesia',
                'postal_code' => '67890',
                'phone' => '081234567893',
                'email' => 'jane@example.com',
                'parent_mother' => 'Mary Smith',
                'parent_mother_phone' => '081234567894',
                'parent_father' => 'John Smith',
                'parent_father_phone' => '081234567895',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'name' => 'Alice Johnson',
                'nickname' => 'Ali',
                'birthdate' => '2002-03-03',
                'address' => '789 Oak St',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'country' => 'Indonesia',
                'postal_code' => '11223',
                'phone' => '081234567896',
                'email' => 'alice@example.com',
                'parent_mother' => 'Anna Johnson',
                'parent_mother_phone' => '081234567897',
                'parent_father' => 'Alan Johnson',
                'parent_father_phone' => '081234567898',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'name' => 'Bob Brown',
                'nickname' => 'Bobby',
                'birthdate' => '2003-04-04',
                'address' => '101 Pine St',
                'city' => 'Medan',
                'province' => 'Sumatera Utara',
                'country' => 'Indonesia',
                'postal_code' => '33445',
                'phone' => '081234567899',
                'email' => 'bob@example.com',
                'parent_mother' => 'Betty Brown',
                'parent_mother_phone' => '081234567800',
                'parent_father' => 'Bill Brown',
                'parent_father_phone' => '081234567801',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'name' => 'Charlie Davis',
                'nickname' => 'Charlie',
                'birthdate' => '2004-05-05',
                'address' => '202 Cedar St',
                'city' => 'Makassar',
                'province' => 'Sulawesi Selatan',
                'country' => 'Indonesia',
                'postal_code' => '55667',
                'phone' => '081234567802',
                'email' => 'charlie@example.com',
                'parent_mother' => 'Cathy Davis',
                'parent_mother_phone' => '081234567803',
                'parent_father' => 'Carl Davis',
                'parent_father_phone' => '081234567804',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'name' => 'David Evans',
                'nickname' => 'Dave',
                'birthdate' => '2005-06-06',
                'address' => '303 Birch St',
                'city' => 'Yogyakarta',
                'province' => 'DI Yogyakarta',
                'country' => 'Indonesia',
                'postal_code' => '77889',
                'phone' => '081234567805',
                'email' => 'david@example.com',
                'parent_mother' => 'Diana Evans',
                'parent_mother_phone' => '081234567806',
                'parent_father' => 'Dan Evans',
                'parent_father_phone' => '081234567807',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 7,
                'name' => 'Eve Foster',
                'nickname' => 'Eve',
                'birthdate' => '2006-07-07',
                'address' => '404 Maple St',
                'city' => 'Semarang',
                'province' => 'Jawa Tengah',
                'country' => 'Indonesia',
                'postal_code' => '99000',
                'phone' => '081234567808',
                'email' => 'eve@example.com',
                'parent_mother' => 'Ella Foster',
                'parent_mother_phone' => '081234567809',
                'parent_father' => 'Ethan Foster',
                'parent_father_phone' => '081234567810',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 8,
                'name' => 'Frank Green',
                'nickname' => 'Frankie',
                'birthdate' => '2007-08-08',
                'address' => '505 Walnut St',
                'city' => 'Denpasar',
                'province' => 'Bali',
                'country' => 'Indonesia',
                'postal_code' => '11234',
                'phone' => '081234567811',
                'email' => 'frank@example.com',
                'parent_mother' => 'Fiona Green',
                'parent_mother_phone' => '081234567812',
                'parent_father' => 'Fred Green',
                'parent_father_phone' => '081234567813',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 9,
                'name' => 'Grace Harris',
                'nickname' => 'Gracie',
                'birthdate' => '2008-09-09',
                'address' => '606 Chestnut St',
                'city' => 'Palembang',
                'province' => 'Sumatera Selatan',
                'country' => 'Indonesia',
                'postal_code' => '22345',
                'phone' => '081234567814',
                'email' => 'grace@example.com',
                'parent_mother' => 'Gina Harris',
                'parent_mother_phone' => '081234567815',
                'parent_father' => 'George Harris',
                'parent_father_phone' => '081234567816',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 10,
                'name' => 'Henry Irving',
                'nickname' => 'Henry',
                'birthdate' => '2009-10-10',
                'address' => '707 Spruce St',
                'city' => 'Balikpapan',
                'province' => 'Kalimantan Timur',
                'country' => 'Indonesia',
                'postal_code' => '33456',
                'phone' => '081234567817',
                'email' => 'henry@example.com',
                'parent_mother' => 'Hannah Irving',
                'parent_mother_phone' => '081234567818',
                'parent_father' => 'Harry Irving',
                'parent_father_phone' => '081234567819',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        return Inertia::render('Duta/Beneficiary/Index', [
            'beneficiaries' => $beneficiaries
        ]);
    }

    /**
     * Display the specified resource.
     */
    // Beneficiary $beneficiary
    public function show($id)
    {
        $beneficiary = [
            'id' => 1,
            'name' => 'John Doe',
            'nickname' => 'John',
            'birthdate' => '2000-01-01',
            'address' => '123 Main St',
            'photo' => 'anak.png',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'country' => 'Indonesia',
            'postal_code' => '12345',
            'phone' => '081234567890',
            'email' => 'john@example.com',
            'parent_mother' => 'Jane Doe',
            'parent_mother_phone' => '081234567891',
            'parent_father' => 'Jack Doe',
            'parent_father_phone' => '081234567892',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        return Inertia::render('Duta/Beneficiary/Show', [
            'beneficiary' => $beneficiary
        ]);
    }
}
