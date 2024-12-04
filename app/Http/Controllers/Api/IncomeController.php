<?php

namespace App\Http\Controllers\Api;

use App\Exports\IncomeExport;
use App\Http\Controllers\Controller;
use App\Models\Ambassador;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Maatwebsite\Excel\Facades\Excel;

class IncomeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ambassador' => 'required|string',
            'transfer_date' => 'required|date',
            'amount' => 'required|numeric|gt:500',
            'donor' => 'required|string|min:1',
            'payment_method' => 'required|string',
            'type' => 'required|string',
            'on_behalf_of' => 'nullable|string|min:1',
            'proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:1024',
        ]);

        if ($request->hasFile('proof')) {
            $proof = $request->file('proof');
            $donor = str_replace(' ', '-', $request->donor);
            $fileName = $request->transfer_date . '-' . $donor . '-' . substr(md5(mt_rand()), 0, 10) . '.' . $proof->getClientOriginalExtension();

            $proof->storeAs('images/proof', $fileName, 'public');
        }

        // Extract the name from the ambassador string
        // Extract the code from the ambassador string
        $ambassadorName = explode(' - ', $request->ambassador)[0];
        $ambassadorCode = explode(' - ', $request->ambassador)[1];

        // Find the ambassador by name
        $ambassador = Ambassador::where('code', $ambassadorCode)->orWhere('name', 'like', '%' . $ambassadorName . '%')->first();

        if (!$ambassador) {
            return response()->json(['error' => 'Ambassador not found'], 404);
        }

        Income::create([
            'ambassador_id' => $ambassador->id,
            'transfer_date' => $request->transfer_date,
            'amount' => $request->amount,
            'donor' => $request->donor,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'on_behalf_of' => $request->on_behalf_of,
            'proof' => isset($fileName) ? $fileName : null,
        ]);

        return response()->json(['message' => 'Income data successfully added'], 201);
    }

    /**
     * Export income data based on given filters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $type = $request->input('type') ?? 'pdf';
        $idsInput = $request->input('ids');

        // Normalize ids input
        $ids = null;
        if ($idsInput) {
            $ids = is_string($idsInput) ? json_decode($idsInput, true) : $idsInput;
            $ids = is_array($ids) ? array_filter($ids) : null;
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
            'team_code' => $filters['team_code'] ?? null,
            'time' => $filters['time'] ?? null,
            'start_range' => $filters['start_range'] ?? null,
            'end_range' => $filters['end_range'] ?? null,
            'week' => $filters['week'] ?? null,
            'month' => $filters['month'] ?? null,
            'year' => $filters['year'] ?? null,
        ];

        if ($filters['team_code'] === 'default') {
            $filters['team_code'] = null;
        }

        if ($filters['time'] === 'default') {
            $filters['time'] =
                $filters['start_range'] =
                $filters['end_range'] =
                $filters['week'] =
                $filters['month'] =
                $filters['year'] = null;
        }

        if ($filters['name'] === 'default') {
            $filters['name'] = null;
        } else {
            $filters['name'] = substr($filters['name'], strpos($filters['name'], ' ') + 1);
        }

        if ($ids && is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        if ($ids && is_array($ids) && count($ids) > 0) {
            $incomes = Income::with('ambassador')->whereIn('id', $ids)->get();

            // If no incomes found with given ids, return error
            if ($incomes->isEmpty()) {
                return response()->json(['error' => 'No incomes found for the provided ids'], 404);
            }
        } else {
            // Build query based on filters
            $incomes = Income::with('ambassador')
                ->when($filters['name'], function ($query, $filters) {
                    return $query->whereHas('ambassador', function ($q) use ($filters) {
                        $q->where('name', 'LIKE', '%' . $filters['name'] . '%');
                    });
                })
                ->when($filters['team_code'], function ($query, $filters) {
                    return $query->whereHas('ambassador', function ($q) use ($filters) {
                        $q->where('code', 'LIKE', $filters['team_code'] . '%');
                    });
                })
                ->when($filters['time'] === 'custom' && $filters['start_range'] && $filters['end_range'], function ($query) use ($filters) {
                    return $query->whereBetween('transfer_date', [$filters['start_range'], $filters['end_range']]);
                })
                ->when($filters['time'] === 'weekly' && $filters['week'], function ($query) use ($filters) {
                    $start = Carbon::parse($filters['week']);
                    $end = $start->copy()->addDays(6);
                    return $query->whereBetween('transfer_date', [$start, $end]);
                })
                ->when($filters['time'] === 'monthly' && $filters['month'] && $filters['year'], function ($query) use ($filters) {
                    return $query->whereMonth('transfer_date', $filters['month'])
                        ->whereYear('transfer_date', $filters['year']);
                })
                ->when($filters['time'] === 'yearly' && $filters['year'], function ($query) use ($filters) {
                    return $query->whereYear('transfer_date', $filters['year']);
                })
                ->orderByDesc('created_at')
                ->get();

            // If no incomes found after applying filters
            if ($incomes->isEmpty()) {
                return response()->json(['error' => 'No incomes found matching the specified filters'], 404);
            }
        }

        // SETTING UP TEMPLATE FOR REPORT
        $template = view('exports.incomes', ['incomes' => $incomes])->render();

        // EXPORT REPORT BASED ON TYPE
        $filename = Carbon::now()->format('Y-m-d_H-i-s');

        switch ($type) {
            case 'pdf':
                $pdfData = Browsershot::html($template)
                    ->setPaper('a4')
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
                return Excel::download(new IncomeExport($incomes), $filename . '.xlsx');

            default:
                return response()->json(['error' => 'Invalid export type'], 400);
        }
    }
}
