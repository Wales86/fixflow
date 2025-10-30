<?php

namespace App\Http\Controllers;

use App\Dto\Report\GetMechanicPerformanceReportData;
use App\Dto\Report\GetTeamPerformanceReportData;
use App\Http\Requests\Report\FilterReportsRequest;
use App\Services\ReportService;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(
        public ReportService $reportService
    ) {
    }

    public function index(FilterReportsRequest $request): Response
    {
        $validated = $request->validated();

        $mechanicPerformanceReport = null;
        if (! empty($validated['mechanic_id'])) {
            $mechanicParams = GetMechanicPerformanceReportData::from($validated);
            $mechanicPerformanceReport = $this->reportService->getMechanicPerformanceReport($mechanicParams);
        }

        $teamParams = GetTeamPerformanceReportData::from($validated);
        $teamPerformanceReport = $this->reportService->getTeamPerformanceReport($teamParams);

        return Inertia::render('reports/index', [
            'teamPerformanceReport' => $teamPerformanceReport,
            'mechanicPerformanceReport' => $mechanicPerformanceReport,
            'mechanics' => $this->reportService->getActiveMechanics(),
        ]);
    }
}
