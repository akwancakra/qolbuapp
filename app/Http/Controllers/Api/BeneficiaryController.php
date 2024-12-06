<?php

namespace App\Http\Controllers\Api;

use App\Exports\BeneficiaryExport;
use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Maatwebsite\Excel\Facades\Excel;

class BeneficiaryController extends Controller
{
    /**
     * Export beneficiary data based on given filters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $type = $request->input('type') ?? 'pdf';
        $niksInput = $request->input('niks');

        // Normalize niks input
        $niks = null;
        if ($niksInput) {
            $niks = is_string($niksInput) ? json_decode($niksInput, true) : $niksInput;
            $niks = is_array($niks) ? array_filter($niks) : null;
        }

        // Normalize filters input
        $filtersInput = $request->input('filters');
        $filters = is_string($filtersInput)
            ? (json_decode($filtersInput, true) ?? [])
            : ($filtersInput ?? []);

        if (!is_array($filters)) {
            $filters = [];
        }

        $filters = [
            'name' => $filters['name'] ?? null,
            'min_age' => $filters['min_age'] ?? null,
            'max_age' => $filters['max_age'] ?? null,
            'education' => $filters['education'] ?? null,
            'school_grade' => $filters['school_grade'] ?? null,
            'shirt_size' => $filters['shirt_size'] ?? null,
            'shoe_size' => $filters['shoe_size'] ?? null,
            'gender' => $filters['gender'] ?? null,
            'status' => $filters['status'] ?? null,
            'sort_by' => $filters['sort_by'] ?? null,
            'sort_direction' => $filters['sort_direction'] ?? null,
            'count_per_page' => $filters['count_per_page'] ?? null,
        ];

        Log::info($niks, $filters);

        if ($niks && is_string($niks)) {
            $niks = json_decode($niks, true);
        }

        if ($niks && is_array($niks) && count($niks) > 0) {
            $beneficiaries = Beneficiary::whereIn('nik', $niks)->get();

            // If no beneficiaries found with given niks, return error
            if ($beneficiaries->isEmpty()) {
                return response()->json(['error' => 'No beneficiaries found for the provided niks'], 404);
            }
        } else {
            // Build query based on filters
            $beneficiaries = Beneficiary::query()
                ->search($filters['name'])
                ->age($filters['min_age'], $filters['max_age'])
                ->lastEducation($filters['education'])
                ->schoolGrade($filters['school_grade'])
                ->gender($filters['gender'])
                ->status($filters['status'])
                ->shirt($filters['shirt_size'])
                ->shoe($filters['shoe_size'])
                ->sortBy($filters['sort_by'], $filters['sort_direction'])
                ->get();

            // If no beneficiaries found after applying filters
            if ($beneficiaries->isEmpty()) {
                return response()->json(['error' => 'No beneficiaries found matching the specified filters'], 404);
            }
        }

        // SETTING UP TEMPLATE FOR REPORT
        $template = view('exports.beneficiaries', ['beneficiaries' => $beneficiaries])->render();

        // EXPORT REPORT BASED ON TYPE
        $filename = Carbon::now()->format('Y-m-d_H-i-s');

        switch ($type) {
            case 'pdf':
                $pdfData = Browsershot::html($template)
                    ->setPaper('a4')
                    ->landscape()
                    ->addChromiumArguments(['--no-sandbox', '--disable-setuid-sandbox'])
                    ->pdf();

                return FacadesResponse::make($pdfData, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '.pdf"',
                ]);

            case 'jpeg':
                $jpegData = Browsershot::html($template)
                    ->setScreenshotType('jpeg')
                    ->fullPage()
                    ->screenshot();

                return FacadesResponse::make($jpegData, 200, [
                    'Content-Type' => 'image/jpeg',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '.jpeg"',
                ]);

            case 'xlsx':
                return Excel::download(new BeneficiaryExport($beneficiaries), $filename . '.xlsx');

            default:
                return response()->json(['error' => 'Invalid export type'], 400);
        }
    }

    public function exportByNik(Request $request, $nik)
    {
        $beneficiary = Beneficiary::where('nik', $nik)->first();

        // Before rendering the view
        $defaultUserImagePath = public_path('assets/images/user-default.jpg');
        $defaultUserImage = file_exists($defaultUserImagePath) ? file_get_contents($defaultUserImagePath) : '';

        $beneficiary->photo_base64 = $beneficiary->photo
            ? base64_encode(file_get_contents(public_path('storage/images/beneficiaries/' . $beneficiary->photo)))
            : base64_encode($defaultUserImage);

        $beneficiary->father_photo_base64 = $beneficiary->father_photo
            ? base64_encode(file_get_contents(public_path('storage/images/beneficiaries/' . $beneficiary->father_photo)))
            : base64_encode($defaultUserImage);

        $beneficiary->mother_photo_base64 = $beneficiary->mother_photo
            ? base64_encode(file_get_contents(public_path('storage/images/beneficiaries/' . $beneficiary->mother_photo)))
            : base64_encode($defaultUserImage);

        if (!$beneficiary) {
            return response()->json(['error' => 'Beneficiary not found'], 404);
        }

        // SETTING UP TEMPLATE FOR REPORT
        $template = view('exports.detail-beneficiary', ['beneficiary' => $beneficiary])->render();

        // EXPORT ONLY PDF
        // FILENAME USE BENEFICARY NAME
        $filename = $beneficiary->name . '_' . Carbon::now()->format('Y-m-d_H-i-s');

        $pdfData = Browsershot::html($template)
            ->setPaper('a4')
            ->addChromiumArguments(['--no-sandbox', '--disable-setuid-sandbox'])
            ->pdf();

        return FacadesResponse::make($pdfData, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.pdf"',
        ]);
    }
}
