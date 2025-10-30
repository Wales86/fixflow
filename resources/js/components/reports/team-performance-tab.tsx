import { StatCard } from '@/components/stat-card';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DateRangePicker } from '@/components/ui/date-range-picker';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { router } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Clock, Users, Wrench } from 'lucide-react';
import { useState } from 'react';
import { type DateRange } from 'react-day-picker';
import {
    Bar,
    BarChart,
    CartesianGrid,
    Legend,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

interface TeamPerformanceTabProps {
    data: App.Dto.Report.TeamPerformanceReportData;
}

export function TeamPerformanceTab({ data }: TeamPerformanceTabProps) {
    const { t } = useLaravelReactI18n();
    const [dateRange, setDateRange] = useState<DateRange | undefined>();

    const handleDateRangeChange = (range: DateRange | undefined) => {
        setDateRange(range);

        if (range?.from && range?.to) {
            router.get(
                '/reports',
                {
                    start_date: range.from.toISOString(),
                    end_date: range.to.toISOString(),
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                },
            );
        }
    };

    return (
        <div className="space-y-6">
            {/* Date Range Filter */}
            <div className="flex items-center justify-between">
                <div>
                    <h3 className="text-lg font-medium">
                        {t('team_performance')}
                    </h3>
                    <p className="text-sm text-muted-foreground">
                        {t('team_performance_description')}
                    </p>
                </div>
                <DateRangePicker
                    value={dateRange}
                    onChange={handleDateRangeChange}
                />
            </div>

            {/* KPI Cards */}
            <div className="grid gap-4 md:grid-cols-3">
                <StatCard
                    title={t('total_hours_worked')}
                    value={data.totalHours}
                    icon={Clock}
                    description={t('in_selected_period')}
                />
                <StatCard
                    title={t('total_orders_completed')}
                    value={data.totalOrders}
                    icon={Wrench}
                    description={t('in_selected_period')}
                />
                <StatCard
                    title={t('active_mechanics')}
                    value={data.activeMechanics}
                    icon={Users}
                />
            </div>

            {/* Bar Chart */}
            <Card>
                <CardHeader>
                    <CardTitle>{t('hours_worked_by_mechanic')}</CardTitle>
                </CardHeader>
                <CardContent>
                    <ResponsiveContainer width="100%" height={350}>
                        <BarChart data={data.chartData}>
                            <CartesianGrid strokeDasharray="3 3" />
                            <XAxis
                                dataKey="name"
                                angle={-45}
                                textAnchor="end"
                                height={100}
                            />
                            <YAxis />
                            <Tooltip />
                            <Legend />
                            <Bar
                                dataKey="hours"
                                fill="hsl(var(--primary))"
                                name={t('hours')}
                            />
                        </BarChart>
                    </ResponsiveContainer>
                </CardContent>
            </Card>

            {/* Data Table */}
            <Card>
                <CardHeader>
                    <CardTitle>{t('detailed_mechanic_stats')}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>{t('mechanic')}</TableHead>
                                <TableHead className="text-right">
                                    {t('total_hours')}
                                </TableHead>
                                <TableHead className="text-right">
                                    {t('orders_completed')}
                                </TableHead>
                                <TableHead className="text-right">
                                    {t('avg_time_per_order')}
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.tableData.length > 0 ? (
                                data.tableData.map((row, index) => (
                                    <TableRow key={index}>
                                        <TableCell className="font-medium">
                                            {row.mechanic}
                                        </TableCell>
                                        <TableCell className="text-right">
                                            {row.totalHours}h
                                        </TableCell>
                                        <TableCell className="text-right">
                                            {row.ordersCompleted}
                                        </TableCell>
                                        <TableCell className="text-right">
                                            {row.avgTimePerOrder.toFixed(1)}h
                                        </TableCell>
                                    </TableRow>
                                ))
                            ) : (
                                <TableRow>
                                    <TableCell
                                        colSpan={4}
                                        className="text-center text-muted-foreground"
                                    >
                                        {t('no_data_available')}
                                    </TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    );
}
