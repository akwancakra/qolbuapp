<?php

namespace App\Http\Controllers\BoardMember;

use App\Http\Controllers\Controller;
use App\Models\Ambassador;
use App\Models\Income;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('BoardMember/Incomes/Index', [
            'incomes' => Income::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('BoardMember/Incomes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ambassador_name' => 'nullable|string',
            'transfer_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|gt:0',
            'donor' => 'required|string',
            'team' => 'nullable|integer',
            'payment_method' => 'required|string',
            'type' => 'required|string',
            'on_behalf_of' => 'nullable|string',
        ]);

        Income::create([
            'ambassador_id' => $request->ambassador_name
                ? Ambassador::firstOrCreate(['name' => $request->ambassador_name])->id
                : null,
            'transfer_date' => $request->transfer_date,
            'amount' => $request->amount,
            'donor' => $request->donor,
            'team' => $request->team,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'on_behalf_of' => $request->on_behalf_of,
        ]);

        return redirect()->route('board_member.incomes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income)
    {
        return Inertia::render('BoardMember/Incomes/Edit', [
            'income' => $income
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Income $income)
    {
        $request->validate([
            'ambassador_name' => 'nullable|string',
            'transfer_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|gt:0',
            'donor' => 'required|string',
            'team' => 'nullable|integer',
            'payment_method' => 'required|string',
            'type' => 'required|string',
            'on_behalf_of' => 'nullable|string',
        ]);

        $income->update([
            'ambassador_id' => $request->ambassador_name
                ? Ambassador::firstOrCreate(['name' => $request->ambassador_name])->id
                : null,
            'transfer_date' => $request->transfer_date,
            'amount' => $request->amount,
            'donor' => $request->donor,
            'team' => $request->team,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'on_behalf_of' => $request->on_behalf_of,
        ]);

        return redirect()->route('board_member.incomes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income)
    {
        $income->delete();

        return redirect()->route('board_member.incomes.index');
    }
}
