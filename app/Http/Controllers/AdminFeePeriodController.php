<?php

namespace App\Http\Controllers;

use App\Models\FeePeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminFeePeriodController extends Controller
{
    /**
     * Display a listing of fee periods grouped by type.
     */
    public function index(): View
    {
        if (! Schema::hasTable('fee_periods')) {
            return view('admin.fees.periods.index', [
                'schoolYears' => collect(),
                'semesters' => collect(),
                'periodsTableMissing' => true,
            ]);
        }

        $schoolYears = FeePeriod::ofType(FeePeriod::TYPE_SCHOOL_YEAR)
            ->ordered()
            ->get();

        $semesters = FeePeriod::ofType(FeePeriod::TYPE_SEMESTER)
            ->ordered()
            ->get();

        return view('admin.fees.periods.index', [
            'schoolYears' => $schoolYears,
            'semesters' => $semesters,
            'periodsTableMissing' => false,
        ]);
    }

    /**
     * Show the form for creating a new fee period.
     */
    public function create(): View
    {
        return view('admin.fees.periods.create', [
            'feePeriod' => new FeePeriod,
        ]);
    }

    /**
     * Store a newly created fee period in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);

        FeePeriod::create($validated);

        return redirect()->route('admin.fees.periods.index')
            ->with('success', 'Fee period created successfully.');
    }

    /**
     * Show the form for editing the specified fee period.
     */
    public function edit(FeePeriod $period): View
    {
        return view('admin.fees.periods.edit', [
            'feePeriod' => $period,
        ]);
    }

    /**
     * Update the specified fee period in storage.
     */
    public function update(Request $request, FeePeriod $period): RedirectResponse
    {
        $validated = $this->validateData($request, $period->id);

        $period->update($validated);

        return redirect()->route('admin.fees.periods.index')
            ->with('success', 'Fee period updated successfully.');
    }

    /**
     * Remove the specified fee period from storage.
     */
    public function destroy(FeePeriod $period): RedirectResponse
    {
        $period->delete();

        return redirect()->route('admin.fees.periods.index')
            ->with('success', 'Fee period deleted successfully.');
    }

    /**
     * Validate incoming request data for fee periods.
     */
    private function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'type' => ['required', Rule::in([FeePeriod::TYPE_SCHOOL_YEAR, FeePeriod::TYPE_SEMESTER])],
            'label' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fee_periods')->ignore($id)->where(fn ($query) => $query->where('type', $request->input('type'))),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ], [
            'label.unique' => 'This label already exists for the selected type.',
        ]) + [
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ];
    }
}
