<?php

namespace App\Services;

use App\Dto\Report\GetTeamPerformanceReportData;
use App\Dto\Report\MechanicChartData;
use App\Dto\Report\MechanicPerformanceStatsData;
use App\Dto\Report\TeamPerformanceReportData;
use App\Models\Mechanic;
use App\Models\RepairOrder;
use App\Models\TimeEntry;
use Carbon\Carbon;

class ReportService
{
    public function getTeamPerformanceReport(GetTeamPerformanceReportData $params): TeamPerformanceReportData
    {
        return TeamPerformanceReportData::from([
            'totalHours' => $this->getTotalHours($params->startDate, $params->endDate),
            'totalOrders' => $this->getTotalCompletedOrders($params->startDate, $params->endDate),
            'activeMechanics' => $this->getActiveMechanicsCount(),
            'chartData' => $this->getChartData($params->startDate, $params->endDate),
            'tableData' => $this->getTableData($params->startDate, $params->endDate),
        ]);
    }

    private function getTotalHours(Carbon $start, Carbon $end): int
    {
        $totalMinutes = TimeEntry::query()
            ->whereBetween('created_at', [$start, $end])
            ->sum('duration_minutes');

        return (int) ($totalMinutes / 60);
    }

    private function getTotalCompletedOrders(Carbon $start, Carbon $end): int
    {
        return RepairOrder::query()
            ->whereHas('timeEntries', function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            })
            ->where('status', 'closed')
            ->count();
    }

    private function getActiveMechanicsCount(): int
    {
        return Mechanic::where('is_active', true)->count();
    }

    private function getMechanicStats(Carbon $start, Carbon $end)
    {
        return TimeEntry::query()
            ->whereBetween('created_at', [$start, $end])
            ->with('mechanic')
            ->get()
            ->groupBy('mechanic_id')
            ->map(function ($entries) {
                $mechanic = $entries->first()->mechanic;
                if (! $mechanic) {
                    return null;
                }

                $totalMinutes = $entries->sum('duration_minutes');
                $totalHours = (int) ($totalMinutes / 60);
                $ordersCount = $entries->pluck('repair_order_id')->unique()->count();
                $avgTimePerOrder = $ordersCount > 0 ? round($totalHours / $ordersCount, 1) : 0;

                return [
                    'mechanic' => $mechanic->first_name.' '.$mechanic->last_name,
                    'totalHours' => $totalHours,
                    'ordersCompleted' => $ordersCount,
                    'avgTimePerOrder' => $avgTimePerOrder,
                ];
            })
            ->filter() // Remove null values (when mechanic was deleted but time entries remain)
            ->sortByDesc('totalHours')
            ->values(); // Reset array keys to sequential (0,1,2...) instead of mechanic_id keys from groupBy
    }

    private function getChartData(Carbon $start, Carbon $end)
    {
        $stats = $this->getMechanicStats($start, $end);

        return $stats->take(10)->map(function ($stat) {
            return MechanicChartData::from([
                'name' => $stat['mechanic'],
                'hours' => $stat['totalHours'],
            ]);
        });
    }

    private function getTableData(Carbon $start, Carbon $end)
    {
        $stats = $this->getMechanicStats($start, $end);

        return $stats->map(function ($stat) {
            return MechanicPerformanceStatsData::from($stat);
        });
    }
}
