<?php

namespace App\Http\Controllers\Ambassador;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BeneficiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Ambassador/Beneficiaries/Index', [
            'beneficiaries' => Beneficiary::all()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Beneficiary $beneficiary)
    {
        return Inertia::render('Ambassador/Beneficiaries/Show', [
            'beneficiary' => $beneficiary
        ]);
    }
}
