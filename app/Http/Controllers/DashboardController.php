<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function index(): Response
    {
        $dashboardData = $this->dashboardService->getDashboardData();

        return Inertia::render('dashboard/index', $dashboardData);
    }
}
