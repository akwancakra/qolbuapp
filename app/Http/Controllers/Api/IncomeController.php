<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ambassador;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IncomeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ambassador' => 'required|string',
            'transfer_date' => 'required|date',
            'amount' => 'required|numeric|gt:500',
            'donor' => 'required|string|min:1',
            'payment_method' => 'required|string',
            'type' => 'required|string',
            'on_behalf_of' => 'nullable|string|min:1',
            'proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:1024',
        ]);

        Log::info('Income data received', $request->all());

        if ($request->hasFile('proof')) {
            $proof = $request->file('proof');
            $donor = str_replace(' ', '-', $request->donor);
            $fileName = $request->transfer_date . '-' . $donor . '-' . substr(md5(mt_rand()), 0, 10) . '.' . $proof->getClientOriginalExtension();

            $proof->storeAs('images/proof', $fileName, 'public');
        }

        // Extract the name from the ambassador string
        // Extract the code from the ambassador string
        $ambassadorName = explode(' - ', $request->ambassador)[0];
        $ambassadorCode = explode(' - ', $request->ambassador)[1];

        // Find the ambassador by name
        $ambassador = Ambassador::where('code', $ambassadorCode)->orWhere('name', 'like', '%' . $ambassadorName . '%')->first();

        if (!$ambassador) {
            return response()->json(['error' => 'Ambassador not found'], 404);
        }

        Income::create([
            'ambassador_id' => $ambassador->id,
            'transfer_date' => $request->transfer_date,
            'amount' => $request->amount,
            'donor' => $request->donor,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'on_behalf_of' => $request->on_behalf_of,
            'proof' => isset($fileName) ? $fileName : null,
        ]);

        return response()->json(['message' => 'Income data successfully added'], 201);
    }
}
