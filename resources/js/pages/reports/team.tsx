import { TeamPerformanceTab } from '@/components/reports/team-performance-tab';
import AppLayout from '@/layouts/app-layout';
import reports from '@/routes/reports';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reports',
        href: reports.team().url,
    },
    {
        title: 'Team Performance',
        href: reports.team().url,
    },
];

interface TeamPerformancePageProps {
    teamPerformanceReport?: App.Dto.Report.TeamPerformanceReportData;
}

export default function TeamPerformancePage({
    teamPerformanceReport,
}: TeamPerformancePageProps) {
    const { t } = useLaravelReactI18n();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('team_performance')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <TeamPerformanceTab data={teamPerformanceReport} />
            </div>
        </AppLayout>
    );
}
