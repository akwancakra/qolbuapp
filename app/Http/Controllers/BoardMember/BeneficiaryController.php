<?php

namespace App\Http\Controllers\BoardMember;

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

        return Inertia::render('BoardMember/Beneficiary/Index', compact('beneficiaries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('BoardMember/Beneficiary/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Beneficiary::create(
            $request->validate([
                'name' => 'required|string',
                'birth_place' => 'required|string',
                'birth_date' => 'required|date',
                'gender' => 'required|alpha:ascii',
                'neighborhood_unit' => 'required|string',
                'father' => 'required|string',
                'mother' => 'required|string',
                'education_level' => 'required|string',
                'school_grade' => 'nullable|integer',
                'shirt_size' => 'nullable|string',
                'shoe_size' => 'nullable|integer',
                'status' => ['nullable', Rule::in(['Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa'])],
                'phone_number' => 'nullable|string',
                'death_certificate_number' => 'nullable|string',
            ])
        );

        return redirect()->route('board_member.beneficiaries.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Beneficiary $beneficiary)
    {
        return Inertia::render('BoardMember/Beneficiary/Show', compact('beneficiary'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Beneficiary $beneficiary)
    {
        return Inertia::render('BoardMember/Beneficiary/Edit', compact('beneficiary'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Beneficiary $beneficiary)
    {
        $beneficiary->update(
            $request->validate([
                'name' => 'required|string',
                'birth_place' => 'required|string',
                'birth_date' => 'required|date|before_or_equal:today',
                'gender' => ['required', Rule::in('L', 'P')],
                'neighborhood_unit' => 'required|string',
                'father' => 'required|string',
                'mother' => 'required|string',
                'education_level' => 'required|string',
                'school_grade' => 'nullable|integer',
                'shirt_size' => 'nullable|string',
                'shoe_size' => 'nullable|integer',
                'status' => ['nullable', Rule::in(['Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa'])],
                'phone_number' => 'nullable|string|digits_between:7,15',
                'death_certificate_number' => 'nullable|string',
            ])
        );

        return redirect()->route('board_member.beneficiaries.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Beneficiary $beneficiary)
    {
        $beneficiary->delete();

        return redirect()->route('board_member.beneficiaries.index');
    }
}
