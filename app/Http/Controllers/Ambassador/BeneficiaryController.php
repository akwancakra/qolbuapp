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
            'name' => 'nullable|string',
            'min_age' => 'nullable|integer|min:0|max:100|lte:max_age',
            'max_age' => 'nullable|integer|min:0|max:100|gte:min_age',
            'education' => 'nullable|string',
            'school_grade' => 'nullable|string|min:0|max:14',
            'shirt_size' => 'nullable|string',
            'shoe_size' => 'nullable|integer',
            'gender' => 'nullable|string|in:default,male,female',
            'status' => ['nullable', Rule::in(['default', 'Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa'])],
            'sort_by' => 'nullable|string',
            'sort_direction' => 'nullable|string|in:asc,desc',
            'count_per_page' => 'nullable|string',
        ]);

        $countPerPage = (int)($request->count_per_page ?? 10);

        $beneficiaries = Beneficiary::query()
            ->search($request->name)
            ->age($request->min_age, $request->max_age)
            ->lastEducation($request->education)
            ->schoolGrade($request->school_grade)
            ->gender($request->gender)
            ->status($request->status)
            ->shirt($request->shirt_size)
            ->shoe($request->shoe_size)
            ->sortBy($request->input('sort_by', 'created_at'), $request->input('sort_direction', 'desc'))
            ->paginate($countPerPage);

        return Inertia::render('Ambassador/Beneficiary/Index', [
            'beneficiaries' => $beneficiaries,
            'filters' => $request->all(),
            'educationList' => $this->educationList,
            'statusList' => $this->statusList,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Beneficiary $beneficiary)
    {
        return Inertia::render('Ambassador/Beneficiary/Show', compact('beneficiary'));
    }
}
