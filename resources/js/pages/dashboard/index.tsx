import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { StatCard } from '@/components/stat-card';
import { RecentOrdersTable } from '@/components/recent-orders-table';
import { Activity, PackageCheck, Timer } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface DashboardPageProps {
    data: App.Dto.DashboardData;
}

export default function Dashboard({ data }: DashboardPageProps) {

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <StatCard
                        title="Aktywne zlecenia"
                        value={data.activeOrdersCount}
                        icon={Activity}
                    />
                    <StatCard
                        title="Gotowe do odbioru"
                        value={data.pendingOrdersCount}
                        icon={PackageCheck}
                    />
                    <StatCard
                        title="Dzisiejszy czas pracy"
                        value={`${data.todayTimeEntriesTotal}h`}
                        icon={Timer}
                    />
                </div>

                <RecentOrdersTable orders={data.recentOrders} />
            </div>
        </AppLayout>
    );
}
