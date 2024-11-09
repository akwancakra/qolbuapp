<?php

namespace App\Http\Controllers\BoardMember;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BeneficiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('board_member.beneficiaries.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('board_member.beneficiaries.create');
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
                'description' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'death_certificate_number' => 'nullable|string',
            ])
        );

        return redirect('/beneficiaries');
    }

    /**
     * Display the specified resource.
     */
    public function show(Beneficiary $beneficiary)
    {
        return view('board_member.beneficiaries.show', [
            'beneficiary' => $beneficiary
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Beneficiary $beneficiary)
    {
        return view('board_member.beneficiaries.edit', [
            'beneficiary' => $beneficiary
        ]);
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
                'description' => 'nullable|string',
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
