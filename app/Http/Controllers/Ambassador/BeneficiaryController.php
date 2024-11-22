<?php

namespace App\Http\Controllers\Ambassador;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BeneficiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|gt:min_age',
            'education_level' => 'nullable|string',
            'school_grade' => 'nullable|integer|gt:0',
            'status' => ['nullable', Rule::in(['Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa'])],
            'sort_by' => 'nullable|string',
            'sort_direction' => 'nullable|string|in:asc,desc',
        ]);

        $beneficiaries = Beneficiary::query()
            ->search($request->search)
            ->age($request->min_age, $request->max_age)
            ->educationLevel($request->education_level)
            ->schoolGrade($request->school_grade)
            ->status($request->status)
            ->sortBy($request->input('sort_by', 'created_at'), $request->input('sort_direction', 'desc'))
            ->paginate(10);

        return Inertia::render('Ambassador/Beneficiary/Index', compact('beneficiaries'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Beneficiary $beneficiary)
    {
        return Inertia::render('Ambassador/Beneficiary/Show', compact('beneficiary'));
    }
}
