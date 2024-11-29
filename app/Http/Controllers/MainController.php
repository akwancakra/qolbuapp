<?php

namespace App\Http\Controllers;

use App\Models\Ambassador;
use App\Models\Income;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableAmbassadors = Ambassador::select('id', 'name', 'code')->get();
        $availableAmbassadors = $availableAmbassadors->map(function ($ambassador) {
            return [
                'label' => $ambassador->code . " " . $ambassador->name,
                'value' => $ambassador->id,
            ];
        });

        return Inertia::render('IncomeCreate', [
            'availableAmbassadors' => $availableAmbassadors,
            'paymentMethods' => $this->paymentMethods,
            'donationTypes' => $this->donationTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ambassador' => 'required|integer',
            'transfer_date' => 'required|date',
            'amount' => 'required|numeric|gt:500',
            'donor' => 'required|string|min:1',
            'team' => 'nullable|integer',
            'payment_method' => 'required|string',
            'type' => 'required|string',
            'on_behalf_of' => 'nullable|string|min:1',
            'proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:1024',
        ]);

        if ($request->hasFile('proof')) {
            $proof = $request->file('proof');
            $donor = str_replace(' ', '-', $request->donor);
            $fileName = $request->transfer_date . '-' . $donor . '-' . substr(md5(mt_rand()), 0, 10) . '.' . $proof->getClientOriginalExtension();

            $proof->storeAs('images/proof', $fileName, 'public');
        }

        Income::create([
            'ambassador_id' => $request->ambassador,
            'transfer_date' => $request->transfer_date,
            'amount' => $request->amount,
            'donor' => $request->donor,
            'team' => $request->team,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'on_behalf_of' => $request->on_behalf_of,
            'proof' => isset($fileName) ? $fileName : null,
        ]);

        return to_route('incomes.create')->with('message', 'Berhasil menambah data pendapatan');
    }
}
