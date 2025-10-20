import { RecentOrdersTable } from '@/components/recent-orders-table';
import { StatCard } from '@/components/stat-card';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { Activity, PackageCheck, Timer } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard({
    activeOrdersCount,
    pendingOrdersCount,
    todayTimeEntriesTotal,
    recentOrders,
}: App.Dto.Dashboard.DashboardData) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <StatCard
                        title="Aktywne zlecenia"
                        value={activeOrdersCount}
                        icon={Activity}
                    />
                    <StatCard
                        title="Gotowe do odbioru"
                        value={pendingOrdersCount}
                        icon={PackageCheck}
                    />
                    <StatCard
                        title="Dzisiejszy czas pracy"
                        value={`${todayTimeEntriesTotal}h`}
                        icon={Timer}
                    />
                </div>

                <RecentOrdersTable orders={recentOrders} />
            </div>
        </AppLayout>
    );
}
