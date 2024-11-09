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
    public function index(Request $request)
    {
        $beneficiaries = Beneficiary::query()
            ->search($request->search)
            ->age($request->min_age, $request->max_age)
            ->educationLevel($request->education_level)
            ->schoolGrade($request->school_grade)
            ->status($request->status)
            ->sortBy($request->input('sort_by', 'created_at'), $request->input('sort_direction', 'desc'))
            ->paginate(10);

        return Inertia::render('Ambassador/Beneficiaries/Index', compact('beneficiaries'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Beneficiary $beneficiary)
    {
        return Inertia::render('Ambassador/Beneficiaries/Show', compact('beneficiary'));
    }
}
