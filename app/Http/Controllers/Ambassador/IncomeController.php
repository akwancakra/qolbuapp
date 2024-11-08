<?php

namespace App\Http\Controllers\Ambassador;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $incomes = Income::all();

        return view('ambassador.incomes.index', [
            'incomes' => $incomes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ambassador.incomes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Income::create(
            $request->validate([
                'ambassador_name' => 'nullable|string',
                'transfer_date' => 'required|date|before_or_equal:today',
                'amount' => 'required|numeric|gt:0',
                'donor' => 'required|string',
                'team' => 'nullable|integer',
                'payment_method' => 'required|string',
                'type' => 'nullable|string',
                'on_behalf_of' => 'nullable|string'
            ])
        );

        return redirect()->route('ambassador.incomes.index');
    }
}
