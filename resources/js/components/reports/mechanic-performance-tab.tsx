import { RepairOrdersExpandableTable } from '@/components/reports/repair-orders-expandable-table';
import { StatCard } from '@/components/stat-card';
import { Card, CardContent } from '@/components/ui/card';
import { DateRangePicker } from '@/components/ui/date-range-picker';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { router } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Clock, TrendingUp, Wrench } from 'lucide-react';
import { useState } from 'react';
import { type DateRange } from 'react-day-picker';

interface MechanicPerformanceTabProps {
    mechanics: App.Dto.Report.MechanicSelectOptionData[];
    data?: App.Dto.Report.MechanicPerformanceReportData | null;
}

export function MechanicPerformanceTab({
    mechanics,
    data,
}: MechanicPerformanceTabProps) {
    const { t } = useLaravelReactI18n();
    const [dateRange, setDateRange] = useState<DateRange | undefined>();
    const [selectedMechanicId, setSelectedMechanicId] = useState<
        string | undefined
    >();

    const handleDateRangeChange = (range: DateRange | undefined) => {
        setDateRange(range);

        if (range?.from && range?.to && selectedMechanicId) {
            fetchMechanicData(selectedMechanicId, range);
        }
    };

    const handleMechanicChange = (mechanicId: string) => {
        setSelectedMechanicId(mechanicId);
        fetchMechanicData(mechanicId, dateRange);
    };

    const fetchMechanicData = (
        mechanicId: string,
        range: DateRange | undefined,
    ) => {
        const params: Record<string, string> = {
            mechanic_id: mechanicId,
        };

        if (range?.from && range?.to) {
            params.start_date = range.from.toISOString();
            params.end_date = range.to.toISOString();
        }

        router.get('/reports', params, {
            preserveState: true,
            preserveScroll: true,
            only: ['mechanicPerformanceReport'],
        });
    };

    return (
        <div className="space-y-6">
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 className="text-lg font-medium">
                        {t('mechanic_performance')}
                    </h3>
                    <p className="text-sm text-muted-foreground">
                        {t('mechanic_performance_description')}
                    </p>
                </div>
                <div className="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <Select
                        value={selectedMechanicId}
                        onValueChange={handleMechanicChange}
                    >
                        <SelectTrigger className="w-full sm:w-[240px]">
                            <SelectValue placeholder={t('select_mechanic')} />
                        </SelectTrigger>
                        <SelectContent>
                            {mechanics.map((mechanic) => (
                                <SelectItem
                                    key={mechanic.id}
                                    value={mechanic.id.toString()}
                                >
                                    {mechanic.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <DateRangePicker
                        value={dateRange}
                        onChange={handleDateRangeChange}
                        disabled={!selectedMechanicId}
                    />
                </div>
            </div>

            {!data && (
                <Card>
                    <CardContent className="flex min-h-[400px] items-center justify-center p-6">
                        <p className="text-muted-foreground">
                            {t('select_mechanic_to_view_performance')}
                        </p>
                    </CardContent>
                </Card>
            )}

            {data && (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
                        <StatCard
                            title={t('total_hours_worked')}
                            value={data.totalHours}
                            icon={Clock}
                            description={t('in_selected_period')}
                        />
                        <StatCard
                            title={t('orders_completed')}
                            value={data.ordersCompleted}
                            icon={Wrench}
                            description={t('in_selected_period')}
                        />
                        <StatCard
                            title={t('avg_time_per_order')}
                            value={`${data.avgTimePerOrder.toFixed(1)}h`}
                            icon={TrendingUp}
                            description={t('average_completion_time')}
                        />
                    </div>

                    <RepairOrdersExpandableTable orders={data.repairOrders} />
                </>
            )}
        </div>
    );
}
