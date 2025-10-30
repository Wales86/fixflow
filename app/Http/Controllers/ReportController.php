<?php

namespace App\Http\Controllers;

use App\Dto\Report\GetTeamPerformanceReportData;
use App\Http\Requests\ReportIndexRequest;
use App\Services\ReportService;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(
        public ReportService $reportService
    ) {}

    public function index(ReportIndexRequest $request): Response
    {
        $params = GetTeamPerformanceReportData::from($request->validated());

        $teamPerformanceReport = $this->reportService->getTeamPerformanceReport($params);

        return Inertia::render('reports/index', [
            'teamPerformanceReport' => $teamPerformanceReport,
        ]);
    }
}
