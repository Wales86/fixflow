import { RepairOrdersExpandableTable } from '@/components/reports/repair-orders-expandable-table';
import { StatCard } from '@/components/stat-card';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { DateRangePicker } from '@/components/ui/date-range-picker';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { formatMinutes } from '@/lib/utils';
import { router } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Clock, RefreshCw, TrendingUp, Wrench, X } from 'lucide-react';
import { useState } from 'react';
import { type DateRange } from 'react-day-picker';

interface MechanicPerformanceTabProps {
    mechanics: App.Dto.Report.MechanicSelectOptionData[];
    data?: App.Dto.Report.MechanicPerformanceReportData | null;
    filters: {
        mechanic_id: number | null;
        start_date: string | null;
        end_date: string | null;
    };
}

export function MechanicPerformanceTab({
    mechanics,
    data,
    filters,
}: MechanicPerformanceTabProps) {
    const { t } = useLaravelReactI18n();
    const [dateRange, setDateRange] = useState<DateRange | undefined>(
        filters.start_date && filters.end_date
            ? {
                  from: new Date(filters.start_date),
                  to: new Date(filters.end_date),
              }
            : undefined,
    );
    const [selectedMechanicId, setSelectedMechanicId] = useState<string | undefined>(filters.mechanic_id?.toString() ?? undefined);

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

        router.get('/reports/mechanic', params, {
            preserveState: true,
            preserveScroll: true,
            only: ['mechanicPerformanceReport'],
        });
    };

    const handleResetDateRange = () => {
        setDateRange(undefined);
        if (selectedMechanicId) {
            fetchMechanicData(selectedMechanicId, undefined);
        }
    };

    const handleRefresh = () => {
        if (selectedMechanicId) {
            fetchMechanicData(selectedMechanicId, dateRange);
        }
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
                        placeholder={t('pick_a_date_range')}
                    />
                    {dateRange && selectedMechanicId && (
                        <Button
                            variant="outline"
                            size="icon"
                            onClick={handleResetDateRange}
                            title={t('reset_date_range')}
                        >
                            <X className="h-4 w-4" />
                        </Button>
                    )}
                    {selectedMechanicId && (
                        <Button
                            variant="outline"
                            size="icon"
                            onClick={handleRefresh}
                            title={t('refresh')}
                        >
                            <RefreshCw className="h-4 w-4" />
                        </Button>
                    )}
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
                            value={formatMinutes(data.totalMinutes)}
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
                            value={formatMinutes(
                                Math.round(data.avgTimePerOrder),
                            )}
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
