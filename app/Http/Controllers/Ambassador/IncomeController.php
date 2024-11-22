<?php

namespace App\Http\Controllers\Ambassador;

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
    public function index(Request $request)
    {
        $incomes = Income::query()
            ->search($request->search)
            ->team($request->team)
            ->transferDate($request->transfer_date)
            ->sortBy($request->input('sort_by', 'created_at'), $request->input('sort_direction', 'desc'))
            ->paginate(10);

        $topTenAmbassadors = Ambassador::topTenByIncome();

        return Inertia::render('Ambassador/Income/Index', compact('incomes', 'topTenAmbassadors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Ambassador/Income/Create');
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
