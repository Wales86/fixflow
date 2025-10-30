import { MechanicPerformanceTab } from '@/components/reports/mechanic-performance-tab';
import { TeamPerformanceTab } from '@/components/reports/team-performance-tab';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AppLayout from '@/layouts/app-layout';
import reports from '@/routes/reports';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reports',
        href: reports.index().url,
    },
];

interface ReportsIndexProps {
    teamPerformanceReport?: App.Dto.Report.TeamPerformanceReportData;
    mechanics: App.Dto.Report.MechanicSelectOptionData[];
    mechanicPerformanceReport?: App.Dto.Report.MechanicPerformanceReportData | null;
}

export default function ReportsIndex({
    teamPerformanceReport,
    mechanics,
    mechanicPerformanceReport,
}: ReportsIndexProps) {
    const { t } = useLaravelReactI18n();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('reports')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <Tabs defaultValue="team" className="w-full">
                    <TabsList className="w-full justify-start">
                        <TabsTrigger value="team">
                            {t('team_performance')}
                        </TabsTrigger>
                        <TabsTrigger value="mechanic">
                            {t('mechanic_performance')}
                        </TabsTrigger>
                    </TabsList>

                    <TabsContent value="team" className="mt-4">
                        <TeamPerformanceTab data={teamPerformanceReport} />
                    </TabsContent>

                    <TabsContent value="mechanic" className="mt-4">
                        <MechanicPerformanceTab
                            mechanics={mechanics}
                            data={mechanicPerformanceReport}
                        />
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}
