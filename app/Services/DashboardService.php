<?php

namespace App\Services;

use App\Dto\Dashboard\DashboardData;
use App\Dto\Dashboard\RecentOrderData;
use App\Enums\RepairOrderStatus;
use App\Models\RepairOrder;
use App\Models\TimeEntry;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    public function getDashboardData(): DashboardData
    {
        $recentOrders = $this->getRecentOrders();

        return DashboardData::from([
            'activeOrdersCount' => $this->getActiveOrdersCount(),
            'pendingOrdersCount' => $this->getPendingOrdersCount(),
            'todayTimeEntriesTotal' => $this->getTodayTimeEntriesTotal(),
            'recentOrders' => RecentOrderData::collect($recentOrders),
        ]);
    }

    private function getActiveOrdersCount(): int
    {
        return RepairOrder::query()
            ->where('status', '!=', RepairOrderStatus::CLOSED)
            ->count();
    }

    private function getPendingOrdersCount(): int
    {
        return RepairOrder::query()
            ->where('status', RepairOrderStatus::READY_FOR_PICKUP)
            ->count();
    }

    private function getTodayTimeEntriesTotal(): int
    {
        return TimeEntry::query()
            ->whereHas('repairOrder')
            ->whereDate('created_at', today())
            ->sum('duration_minutes');
    }

    private function getRecentOrders(int $limit = 10): Collection
    {
        return RepairOrder::query()
            ->with(['vehicle', 'client'])
            ->latest('updated_at')
            ->limit($limit)
            ->get();
    }
}
