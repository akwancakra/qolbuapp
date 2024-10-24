<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Carbon\Carbon;
use Inertia\Inertia;

class TransactionController extends Controller
{
    // Function untuk menghasilkan tanggal acak dalam format UTC
    private function generateRandomUTCDate()
    {
        // Membuat random timestamp dalam rentang waktu
        $randomTimestamp = rand(0, 100000000);
        return Carbon::now()->subSeconds($randomTimestamp)->toISOString();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = [
            [
                'id' => 1,
                'duta' => 'Duta 1',
                'donatur' => 'Donatur 1',
                'nominal' => '100000',
                'metode' => 'Bank Transfer',
                'jenis' => 'Donation',
                'transfer_date' => '2022-01-30T00:00:00.318509Z',
                'created_at' => '2022-01-29T00:27:49.318509Z',
                'updated_at' => '2022-01-29T00:27:49.318509Z',
            ],
            [
                'id' => 2,
                'duta' => 'Duta 2',
                'donatur' => 'Donatur 2',
                'nominal' => '200000',
                'metode' => 'Credit Card',
                'jenis' => 'Donation',
                'transfer_date' => $this->generateRandomUTCDate(),
                'created_at' => $this->generateRandomUTCDate(),
                'updated_at' => $this->generateRandomUTCDate(),
            ],
            [
                'id' => 3,
                'duta' => 'Duta 3',
                'donatur' => 'Donatur 3',
                'nominal' => '300000',
                'metode' => 'Bank Transfer',
                'jenis' => 'Donation',
                'transfer_date' => $this->generateRandomUTCDate(),
                'created_at' => $this->generateRandomUTCDate(),
                'updated_at' => $this->generateRandomUTCDate(),
            ],
            [
                'id' => 4,
                'duta' => 'Duta 4',
                'donatur' => 'Donatur 4',
                'nominal' => '400000',
                'metode' => 'Credit Card',
                'jenis' => 'Donation',
                'transfer_date' => $this->generateRandomUTCDate(),
                'created_at' => $this->generateRandomUTCDate(),
                'updated_at' => $this->generateRandomUTCDate(),
            ],
            [
                'id' => 5,
                'duta' => 'Duta 5',
                'donatur' => 'Donatur 5',
                'nominal' => '500000',
                'metode' => 'Bank Transfer',
                'jenis' => 'Donation',
                'transfer_date' => $this->generateRandomUTCDate(),
                'created_at' => $this->generateRandomUTCDate(),
                'updated_at' => $this->generateRandomUTCDate(),
            ],
            [
                'id' => 6,
                'duta' => 'Duta 6',
                'donatur' => 'Donatur 6',
                'nominal' => '600000',
                'metode' => 'Credit Card',
                'jenis' => 'Donation',
                'transfer_date' => $this->generateRandomUTCDate(),
                'created_at' => $this->generateRandomUTCDate(),
                'updated_at' => $this->generateRandomUTCDate(),
            ],
            [
                'id' => 7,
                'duta' => 'Duta 7',
                'donatur' => 'Donatur 7',
                'nominal' => '700000',
                'metode' => 'Bank Transfer',
                'jenis' => 'Donation',
                'transfer_date' => $this->generateRandomUTCDate(),
                'created_at' => $this->generateRandomUTCDate(),
                'updated_at' => $this->generateRandomUTCDate(),
            ],
            [
                'id' => 8,
                'duta' => 'Duta 8',
                'donatur' => 'Donatur 8',
                'nominal' => '800000',
                'metode' => 'Credit Card',
                'jenis' => 'Donation',
                'transfer_date' => $this->generateRandomUTCDate(),
                'created_at' => $this->generateRandomUTCDate(),
                'updated_at' => $this->generateRandomUTCDate(),
            ],
            [
                'id' => 9,
                'duta' => 'Duta 9',
                'donatur' => 'Donatur 9',
                'nominal' => '900000',
                'metode' => 'Bank Transfer',
                'jenis' => 'Donation',
                'transfer_date' => $this->generateRandomUTCDate(),
                'created_at' => $this->generateRandomUTCDate(),
                'updated_at' => $this->generateRandomUTCDate(),
            ],
            [
                'id' => 10,
                'duta' => 'Duta 10',
                'donatur' => 'Donatur 10',
                'nominal' => '1000000',
                'metode' => 'Credit Card',
                'jenis' => 'Donation',
                'transfer_date' => $this->generateRandomUTCDate(),
                'created_at' => $this->generateRandomUTCDate(),
                'updated_at' => $this->generateRandomUTCDate(),
            ]
        ];

        return Inertia::render('Pengurus/Transaction/Index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Pengurus/Transaction/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    // Transaction $transaction
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    // Transaction $transaction
    public function edit($id)
    {
        $transaction = [
            'id' => 1,
            'duta' => 'Duta 1',
            'donatur' => 'Donatur 1',
            'nominal' => '100000',
            'metode' => 'Credit Card',
            'jenis' => 'Donation',
            'transfer_date' => $this->generateRandomUTCDate(),
            'created_at' => $this->generateRandomUTCDate(),
            'updated_at' => $this->generateRandomUTCDate(),
        ];

        return Inertia::render('Pengurus/Transaction/Edit', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
