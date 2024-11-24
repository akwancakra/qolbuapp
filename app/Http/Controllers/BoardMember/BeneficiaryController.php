<?php

namespace App\Http\Controllers\BoardMember;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BeneficiaryController extends Controller
{
    public $educationList = [
        ['label' => 'Tidak Sekolah', 'value' => 'Tidak Sekolah'],
        ['label' => 'TK (Taman Kanak-kanak)', 'value' => 'TK'],
        ['label' => 'SD (Sekolah Dasar)', 'value' => 'SD'],
        ['label' => 'SMP (Sekolah Menengah Pertama)', 'value' => 'SMP'],
        ['label' => 'SMA (Sekolah Menengah Atas)', 'value' => 'SMA'],
        ['label' => 'D1 (Diploma 1)', 'value' => 'D1'],
        ['label' => 'D2 (Diploma 2)', 'value' => 'D2'],
        ['label' => 'D3 (Diploma 3)', 'value' => 'D3'],
        ['label' => 'S1 (Sarjana)', 'value' => 'S1'],
    ];

    public $statusList = [
        ['label' => 'Yatim', 'value' => 'Yatim'],
        ['label' => 'Piatu', 'value' => 'Piatu'],
        ['label' => 'Yatim Piatu', 'value' => 'Yatim Piatu'],
        ['label' => 'Dhuafa', 'value' => 'Dhuafa'],
    ];

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
        ]);

        // dd(session()->all());

        // BELUM ADA SHIRT SIZE & SHOE SIZE
        $beneficiaries = Beneficiary::query()
            ->search($request->name)
            ->age($request->min_age, $request->max_age)
            ->educationLevel($request->education)
            ->schoolGrade($request->school_grade)
            ->gender($request->gender)
            ->status($request->status)
            ->sortBy($request->input('sort_by', 'created_at'), $request->input('sort_direction', 'desc'))
            ->paginate(10);

        return Inertia::render('BoardMember/Beneficiary/Index', [
            'beneficiaries' => $beneficiaries,
            'filters' => $request->all(),
            'educationList' => $this->educationList,
            'statusList' => $this->statusList,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('BoardMember/Beneficiary/Create', [
            'educationList' => $this->educationList,
            'statusList' => $this->statusList,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Beneficiary::create(
            $request->validate([
                'nik' => 'required|integer',
                'place_of_birth' => 'required|string',
                'date_of_birth' => 'required|date',
                'name' => 'required|string',
                'neighborhood_unit' => 'nullable|string',
                'gender' => 'required|string|in:male,female',
                'last_education' => 'required|string',
                'school_grade' => 'required|string',
                'shirt_size' => 'nullable|string',
                'shoe_size' => 'nullable|integer',
                'father' => 'required|string',
                'mother' => 'required|string',
                'father_death_certificate_number' => 'nullable|string',
                'mother_death_certificate_number' => 'nullable|string',
                'phone_number' => 'required|string|max:15',
                'status' => ['nullable', Rule::in(['Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa'])],
                'description' => 'nullable|string',
            ])
        );

        return to_route('board_member.beneficiaries.index')->with('message', 'Berhasil menambahkan penerima manfaat');
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
        return Inertia::render('BoardMember/Beneficiary/Edit', [
            'beneficiary' => $beneficiary,
            'educationList' => $this->educationList,
            'statusList' => $this->statusList,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Beneficiary $beneficiary)
    {
        $beneficiary->update(
            $request->validate([
                'nik' => 'required|integer',
                'place_of_birth' => 'required|string',
                'date_of_birth' => 'required|date',
                'name' => 'required|string',
                'neighborhood_unit' => 'nullable|string',
                'gender' => 'required|string|in:male,female',
                'last_education' => 'required|string',
                'school_grade' => 'required|string',
                'shirt_size' => 'nullable|string',
                'shoe_size' => 'nullable|integer',
                'father' => 'required|string',
                'mother' => 'required|string',
                'father_death_certificate_number' => 'nullable|string',
                'mother_death_certificate_number' => 'nullable|string',
                'phone_number' => 'required|string|max:15',
                'status' => ['nullable', Rule::in(['Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa'])],
                'description' => 'nullable|string',
            ])
        );

        return to_route('board_member.beneficiaries.index')->with('message', 'Berhasil mengubah penerima manfaat');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Beneficiary $beneficiary)
    {
        $beneficiary->delete();
        return to_route('board_member.beneficiaries.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyMultiple(Request $request): RedirectResponse
    {
        $niks = $request->input('niks');
        Beneficiary::whereIn('nik', $niks)->delete();
        return to_route('board_member.beneficiaries.index')->with('message', 'Berhasil menghapus beberapa penerima manfaat');

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Berhasil menghapus beberapa penerima manfaat',
        // ]);
    }
}
