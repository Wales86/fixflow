import { MechanicPerformanceTab } from '@/components/reports/mechanic-performance-tab';
import AppLayout from '@/layouts/app-layout';
import reports from '@/routes/reports';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reports',
        href: reports.mechanic().url,
    },
    {
        title: 'Mechanic Performance',
        href: reports.mechanic().url,
    },
];

interface MechanicPerformancePageProps {
    mechanics: App.Dto.Report.MechanicSelectOptionData[];
    mechanicPerformanceReport?: App.Dto.Report.MechanicPerformanceReportData | null;
}

export default function MechanicPerformancePage({
    mechanics,
    mechanicPerformanceReport,
}: MechanicPerformancePageProps) {
    const { t } = useLaravelReactI18n();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('mechanic_performance')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <MechanicPerformanceTab
                    mechanics={mechanics}
                    data={mechanicPerformanceReport}
                />
            </div>
        </AppLayout>
    );
}
