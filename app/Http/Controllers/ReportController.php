<?php

namespace App\Http\Controllers;

use App\Dto\Report\GetMechanicPerformanceReportData;
use App\Dto\Report\GetTeamPerformanceReportData;
use App\Http\Requests\Report\FilterMechanicReportRequest;
use App\Http\Requests\Report\FilterTeamReportRequest;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(
        public ReportService $reportService
    ) {}

    public function index(): RedirectResponse
    {
        return redirect()->route('reports.team');
    }

    public function teamPerformance(FilterTeamReportRequest $request): Response
    {
        $validated = $request->validated();

        $teamParams = GetTeamPerformanceReportData::from($validated);
        $teamPerformanceReport = $this->reportService->getTeamPerformanceReport($teamParams);

        return Inertia::render('reports/team', [
            'teamPerformanceReport' => $teamPerformanceReport,
        ]);
    }

    public function mechanicPerformance(FilterMechanicReportRequest $request): Response
    {
        $validated = $request->validated();

        $mechanicPerformanceReport = null;
        if (! empty($validated['mechanic_id'])) {
            $mechanicParams = GetMechanicPerformanceReportData::from($validated);
            $mechanicPerformanceReport = $this->reportService->getMechanicPerformanceReport($mechanicParams);
        }

        return Inertia::render('reports/mechanic', [
            'mechanicPerformanceReport' => $mechanicPerformanceReport,
            'mechanics' => $this->reportService->getActiveMechanics(),
            'filters' => [
                'mechanic_id' => $validated['mechanic_id'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
            ],
        ]);
    }
}
