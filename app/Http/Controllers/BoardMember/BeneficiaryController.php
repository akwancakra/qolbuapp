<?php

namespace App\Http\Controllers\BoardMember;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BeneficiaryController extends Controller
{
    private function storeFile($file, $name, $oldFilename = null)
    {
        if ($oldFilename) {
            $filePath = 'images/beneficiaries/' . $oldFilename;
            Storage::disk('public')->delete($filePath);
        }

        $fileName = str_replace(' ', '-', $name) . '-' . substr(md5(mt_rand()), 0, 10) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('images/beneficiaries', $fileName, 'public');
        return $fileName;
    }

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
            ->educationLevel($request->education)
            ->schoolGrade($request->school_grade)
            ->gender($request->gender)
            ->status($request->status)
            ->shirt($request->shirt_size)
            ->shoe($request->shoe_size)
            ->sortBy($request->input('sort_by', 'created_at'), $request->input('sort_direction', 'desc'))
            ->paginate($countPerPage);

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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'father' => 'required|string',
            'mother' => 'required|string',
            'father_death_certificate_number' => 'nullable|string',
            'father_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'mother_death_certificate_number' => 'nullable|string',
            'mother_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'phone_number' => 'required|string|max:15',
            'status' => ['nullable', Rule::in(['Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa'])],
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $photoFileName = $this->storeFile($request->file('photo'), $request->name);
        }

        if ($request->hasFile('father_photo')) {
            $fatherFileName = $this->storeFile($request->file('father_photo'), $request->father);
        }

        if ($request->hasFile('mother_photo')) {
            $motherFileName = $this->storeFile($request->file('mother_photo'), $request->mother);
        }

        $beneficiary = Beneficiary::create([
            'nik' => $request->nik,
            'place_of_birth' => $request->place_of_birth,
            'date_of_birth' => $request->date_of_birth,
            'name' => $request->name,
            'neighborhood_unit' => $request->neighborhood_unit,
            'gender' => $request->gender,
            'last_education' => $request->last_education,
            'school_grade' => $request->school_grade,
            'shirt_size' => $request->shirt_size,
            'shoe_size' => $request->shoe_size,
            'photo' => isset($photoFileName) ? $photoFileName : null,
            'father' => $request->father,
            'mother' => $request->mother,
            'father_death_certificate_number' => $request->father_death_certificate_number,
            'father_photo' => isset($fatherFileName) ? $fatherFileName : null,
            'mother_death_certificate_number' => $request->mother_death_certificate_number,
            'mother_photo' => isset($motherFileName) ? $motherFileName : null,
            'phone_number' => $request->phone_number,
            'status' => $request->status,
            'description' => $request->description
        ]);

        return to_route('board_member.beneficiaries.show', $beneficiary->nik)->with('message', 'Berhasil menambahkan penerima manfaat');
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'father' => 'required|string',
            'father_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'mother' => 'required|string',
            'mother_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'father_death_certificate_number' => 'nullable|string',
            'mother_death_certificate_number' => 'nullable|string',
            'phone_number' => 'required|string|max:15',
            'status' => ['nullable', Rule::in(['Yatim', 'Piatu', 'Yatim Piatu', 'Dhuafa'])],
            'description' => 'nullable|string',
        ]);

        // dd($request->all());

        if ($request->hasFile('photo')) {
            $photoFileName = $this->storeFile($request->file('photo'), $request->name, $beneficiary->photo);
        }
        if ($request->hasFile('father_photo')) {
            $fatherPhotoFileName = $this->storeFile($request->file('father_photo'), $request->father, $beneficiary->father_photo);
        }

        if ($request->hasFile('mother_photo')) {
            $motherPhotoFileName = $this->storeFile($request->file('mother_photo'), $request->mother, $beneficiary->mother_photo);
        }

        // dd($photoFileName, $motherPhotoFileName, $fatherPhotoFileName);

        $beneficiary->update([
            'nik' => $request->nik,
            'place_of_birth' => $request->place_of_birth,
            'date_of_birth' => $request->date_of_birth,
            'name' => $request->name,
            'neighborhood_unit' => $request->neighborhood_unit,
            'gender' => $request->gender,
            'last_education' => $request->last_education,
            'school_grade' => $request->school_grade,
            'shirt_size' => $request->shirt_size,
            'shoe_size' => $request->shoe_size,
            'photo' => isset($photoFileName) ? $photoFileName : $beneficiary->photo,
            'father' => $request->father,
            'mother' => $request->mother,
            'father_death_certificate_number' => $request->father_death_certificate_number,
            'father_photo' => isset($fatherPhotoFileName) ? $fatherPhotoFileName : $beneficiary->father_photo,
            'mother_death_certificate_number' => $request->mother_death_certificate_number,
            'mother_photo' => isset($motherPhotoFileName) ? $motherPhotoFileName : $beneficiary->mother_photo,
            'phone_number' => $request->phone_number,
            'status' => $request->status,
            'description' => $request->description
        ]);

        return to_route('board_member.beneficiaries.show', $beneficiary->nik)->with('message', 'Berhasil mengubah penerima manfaat');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Beneficiary $beneficiary)
    {
        if ($beneficiary->photo) {
            $filePath = 'images/beneficiaries/' . $beneficiary->photo;
            Storage::disk('public')->delete($filePath);
        }

        if ($beneficiary->father_photo) {
            $filePath = 'images/beneficiaries/' . $beneficiary->father_photo;
            Storage::disk('public')->delete($filePath);
        }

        if ($beneficiary->mother_photo) {
            $filePath = 'images/beneficiaries/' . $beneficiary->mother_photo;
            Storage::disk('public')->delete($filePath);
        }

        $beneficiary->delete();
        return to_route('board_member.beneficiaries.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyMultiple(Request $request): RedirectResponse
    {
        $niks = $request->input('niks');
        $beneficiaries = Beneficiary::whereIn('nik', $niks)->get();

        foreach ($beneficiaries as $beneficiary) {
            if ($beneficiary->photo) {
                $filePath = 'images/beneficiaries/' . $beneficiary->photo;
                Storage::disk('public')->delete($filePath);
            }

            if ($beneficiary->father_photo) {
                $filePath = 'images/beneficiaries/' . $beneficiary->father_photo;
                Storage::disk('public')->delete($filePath);
            }

            if ($beneficiary->mother_photo) {
                $filePath = 'images/beneficiaries/' . $beneficiary->mother_photo;
                Storage::disk('public')->delete($filePath);
            }

            $beneficiary->delete();
        }

        return to_route('board_member.beneficiaries.index')->with('message', 'Berhasil menghapus beberapa penerima manfaat');
    }
}
