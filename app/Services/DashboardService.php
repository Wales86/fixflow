<?php

namespace App\Services;

use App\Dto\Dashboard\DashboardData;
use App\Enums\RepairOrderStatus;
use App\Models\RepairOrder;
use App\Models\TimeEntry;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    public function getDashboardData(): DashboardData
    {
        $recentOrders = $this->getRecentOrders()
            ->map(fn ($order) => [
                'id' => $order->id,
                'vehicle' => "{$order->vehicle->make} {$order->vehicle->model} {$order->vehicle->year}",
                'client' => "{$order->client->first_name} {$order->client->last_name}",
                'status' => $order->status->value,
                'created_at' => $order->created_at->toISOString(),
            ]);

        return DashboardData::from([
            'activeOrdersCount' => $this->getActiveOrdersCount(),
            'pendingOrdersCount' => $this->getPendingOrdersCount(),
            'todayTimeEntriesTotal' => $this->getTodayTimeEntriesTotal(),
            'recentOrders' => $recentOrders->all(),
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
