import { TeamPerformanceTab } from '@/components/reports/team-performance-tab';
import AppLayout from '@/layouts/app-layout';
import reports from '@/routes/reports';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

interface TeamPerformancePageProps {
    teamPerformanceReport?: App.Dto.Report.TeamPerformanceReportData;
}

export default function TeamPerformancePage({
    teamPerformanceReport,
}: TeamPerformancePageProps) {
    const { t } = useLaravelReactI18n();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('reports'),
            href: reports.team().url,
        },
        {
            title: t('team_performance'),
            href: reports.team().url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('team_performance')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <TeamPerformanceTab data={teamPerformanceReport} />
            </div>
        </AppLayout>
    );
}
