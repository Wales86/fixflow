<?php

namespace App\Services;

use App\Dto\Report\GetMechanicPerformanceReportData;
use App\Dto\Report\GetTeamPerformanceReportData;
use App\Dto\Report\MechanicChartData;
use App\Dto\Report\MechanicPerformanceReportData;
use App\Dto\Report\MechanicPerformanceStatsData;
use App\Dto\Report\MechanicSelectOptionData;
use App\Dto\Report\RepairOrderDetailData;
use App\Dto\Report\TeamPerformanceReportData;
use App\Dto\Report\TimeEntryDetailData;
use App\Models\Mechanic;
use App\Models\RepairOrder;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Spatie\LaravelData\DataCollection;

class ReportService
{
    public function getTeamPerformanceReport(GetTeamPerformanceReportData $params): TeamPerformanceReportData
    {
        return TeamPerformanceReportData::from([
            'totalMinutes' => $this->getTotalMinutes($params->startDate, $params->endDate),
            'totalOrders' => $this->getTotalCompletedOrders($params->startDate, $params->endDate),
            'activeMechanics' => $this->getActiveMechanicsCount(),
            'chartData' => $this->getChartData($params->startDate, $params->endDate),
            'tableData' => $this->getTableData($params->startDate, $params->endDate),
        ]);
    }

    private function getTotalMinutes(Carbon $start, Carbon $end): int
    {
        return (int) TimeEntry::query()
            ->whereBetween('created_at', [$start, $end])
            ->sum('duration_minutes');
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
                $ordersCount = $entries->pluck('repair_order_id')->unique()->count();
                $avgTimePerOrder = $ordersCount > 0 ? round($totalMinutes / $ordersCount, 1) : 0;

                return [
                    'mechanicId' => $mechanic->id,
                    'mechanic' => $mechanic->first_name.' '.$mechanic->last_name,
                    'totalMinutes' => $totalMinutes,
                    'ordersCompleted' => $ordersCount,
                    'avgTimePerOrder' => $avgTimePerOrder,
                ];
            })
            ->filter() // Remove null values (when mechanic was deleted but time entries remain)
            ->sortByDesc('totalMinutes')
            ->values(); // Reset array keys to sequential (0,1,2...) instead of mechanic_id keys from groupBy
    }

    private function getChartData(Carbon $start, Carbon $end): DataCollection
    {
        $stats = $this->getMechanicStats($start, $end);

        $chartItems = $stats->take(10)->map(fn ($stat) => MechanicChartData::from([
            'name' => $stat['mechanic'],
            'minutes' => $stat['totalMinutes'],
        ]));

        return new DataCollection(MechanicChartData::class, $chartItems);
    }

    private function getTableData(Carbon $start, Carbon $end): DataCollection
    {
        $stats = $this->getMechanicStats($start, $end);

        $tableItems = $stats->map(fn ($stat) => MechanicPerformanceStatsData::from($stat));

        return new DataCollection(MechanicPerformanceStatsData::class, $tableItems);
    }

    public function getMechanicPerformanceReport(GetMechanicPerformanceReportData $params): MechanicPerformanceReportData
    {
        $mechanic = Mechanic::findOrFail($params->mechanic_id);

        $totalMinutes = TimeEntry::query()
            ->where('mechanic_id', $mechanic->id)
            ->whereBetween('created_at', [$params->startDate, $params->endDate])
            ->sum('duration_minutes');

        $ordersCompleted = TimeEntry::query()
            ->where('mechanic_id', $mechanic->id)
            ->whereBetween('created_at', [$params->startDate, $params->endDate])
            ->distinct('repair_order_id')
            ->count('repair_order_id');

        $avgTimePerOrder = $ordersCompleted > 0 ? round($totalMinutes / $ordersCompleted, 1) : 0;

        $repairOrders = $this->getMechanicRepairOrders($mechanic->id, $params->startDate, $params->endDate);

        return MechanicPerformanceReportData::from([
            'totalMinutes' => $totalMinutes,
            'ordersCompleted' => $ordersCompleted,
            'avgTimePerOrder' => $avgTimePerOrder,
            'repairOrders' => $repairOrders,
        ]);
    }

    public function getActiveMechanics(): DataCollection
    {
        $mechanics = Mechanic::where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->map(fn ($mechanic) => MechanicSelectOptionData::from([
                'id' => $mechanic->id,
                'name' => $mechanic->name,
            ]));

        return new DataCollection(MechanicSelectOptionData::class, $mechanics);
    }

    private function getMechanicRepairOrders(int $mechanicId, Carbon $start, Carbon $end): DataCollection
    {
        $orders = RepairOrder::query()
            ->whereHas('timeEntries', function ($query) use ($mechanicId, $start, $end) {
                $query->where('mechanic_id', $mechanicId)
                    ->whereBetween('created_at', [$start, $end]);
            })
            ->with([
                'vehicle.client',
                'timeEntries' => function ($query) use ($mechanicId, $start, $end) {
                    $query->where('mechanic_id', $mechanicId)
                        ->whereBetween('created_at', [$start, $end])
                        ->orderBy('created_at', 'desc');
                },
            ])
            ->orderBy('started_at', 'desc')
            ->get()
            ->map(function ($order) {
                $totalMinutes = $order->timeEntries->sum('duration_minutes');

                $timeEntries = $order->timeEntries->map(fn ($entry) => TimeEntryDetailData::from([
                    'id' => $entry->id,
                    'date' => $entry->created_at,
                    'durationMinutes' => $entry->duration_minutes,
                    'description' => $entry->description,
                ]));

                return RepairOrderDetailData::from([
                    'id' => $order->id,
                    'vehicleInfo' => $order->vehicle->make.' '.$order->vehicle->model.' ('.$order->vehicle->registration_number.')',
                    'status' => $order->status->value,
                    'startedAt' => $order->started_at?->format('Y-m-d'),
                    'finishedAt' => $order->finished_at?->format('Y-m-d'),
                    'totalMinutes' => $totalMinutes,
                    'timeEntries' => new DataCollection(TimeEntryDetailData::class, $timeEntries),
                ]);
            });

        return new DataCollection(RepairOrderDetailData::class, $orders);
    }
}
