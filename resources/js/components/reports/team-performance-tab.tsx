import { StatCard } from '@/components/stat-card';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { DateRangePicker } from '@/components/ui/date-range-picker';
import { Skeleton } from '@/components/ui/skeleton';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatMinutes } from '@/lib/utils';
import reports from '@/routes/reports';
import { router } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Clock, RefreshCw, Users, Wrench, X } from 'lucide-react';
import { useState } from 'react';
import { type DateRange } from 'react-day-picker';
import {
    Bar,
    BarChart,
    CartesianGrid,
    Label,
    Legend,
    ResponsiveContainer,
    Text,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

interface TeamPerformanceTabProps {
    data?: App.Dto.Report.TeamPerformanceReportData;
}

interface ChartDataItem {
    name: string;
    minutes: number;
}

interface CustomTooltipProps {
    active?: boolean;
    payload?: Array<{
        payload: ChartDataItem;
    }>;
}

function CustomTooltip({ active, payload }: CustomTooltipProps) {
    const { t } = useLaravelReactI18n();

    if (active && payload && payload.length) {
        const data = payload[0].payload;
        return (
            <div className="rounded-lg border bg-card p-2 shadow-md">
                <p className="font-medium text-card-foreground">{data.name}</p>
                <p className="text-sm text-muted-foreground">
                    {t('hours')}: {formatMinutes(data.minutes)}
                </p>
            </div>
        );
    }

    return null;
}

interface CustomYAxisTickProps {
    x: number;
    y: number;
    payload: { value: string };
}

function CustomYAxisTick(props: any) {
    const { x, y, payload } = props;
    const nameParts = payload.value.split(' ');
    const firstName = nameParts[0] || '';
    const lastName = nameParts.slice(1).join(' ') || '';

    return (
        <g transform={`translate(${x},${y})`}>
            <Text x={-10} y={-5} textAnchor="end" className="text-sm fill-current">
                {firstName}
            </Text>
            {lastName && (
                <Text x={-10} y={10} textAnchor="end" className="text-sm fill-current text-muted-foreground">
                    {lastName}
                </Text>
            )}
        </g>
    );
}

export function TeamPerformanceTab({ data }: TeamPerformanceTabProps) {
    const { t } = useLaravelReactI18n();
    const [dateRange, setDateRange] = useState<DateRange | undefined>();

    const handleDateRangeChange = (range: DateRange | undefined) => {
        setDateRange(range);

        if (range?.from && range?.to) {
            router.get(
                '/reports/team',
                {
                    start_date: range.from.toISOString(),
                    end_date: range.to.toISOString(),
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['teamPerformanceReport'],
                },
            );
        }
    };

    const handleResetDateRange = () => {
        setDateRange(undefined);
        router.get(
            '/reports/team',
            {},
            {
                preserveState: true,
                preserveScroll: true,
                only: ['teamPerformanceReport'],
            },
        );
    };

    const handleRefresh = () => {
        const params: Record<string, string> = {};
        if (dateRange?.from && dateRange?.to) {
            params.start_date = dateRange.from.toISOString();
            params.end_date = dateRange.to.toISOString();
        }

        router.get('/reports/team', params, {
            preserveState: true,
            preserveScroll: true,
            only: ['teamPerformanceReport'],
        });
    };

    const handleMechanicClick = (mechanicId: number) => {
        const params: Record<string, string | number> = {
            mechanic_id: mechanicId,
        };
        if (dateRange?.from && dateRange?.to) {
            params.start_date = dateRange.from.toISOString();
            params.end_date = dateRange.to.toISOString();
        }

        router.visit(reports.mechanic({ query: params }).url);
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
                <div className="flex items-center gap-2">
                    <DateRangePicker
                        value={dateRange}
                        onChange={handleDateRangeChange}
                        placeholder={t('pick_a_date_range')}
                    />
                    {dateRange && (
                        <Button
                            variant="outline"
                            size="icon"
                            onClick={handleResetDateRange}
                            title={t('reset_date_range')}
                        >
                            <X className="h-4 w-4" />
                        </Button>
                    )}
                    <Button
                        variant="outline"
                        size="icon"
                        onClick={handleRefresh}
                        title={t('refresh')}
                    >
                        <RefreshCw className="h-4 w-4" />
                    </Button>
                </div>
            </div>

            {!data ? (
                <TeamPerformanceSkeleton />
            ) : (
                <>
                    {/* KPI Cards */}
                    <div className="grid gap-4 md:grid-cols-3">
                        <StatCard
                            title={t('total_hours_worked')}
                            value={formatMinutes(data.totalMinutes)}
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
                            <CardTitle>
                                {t('hours_worked_by_mechanic')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ResponsiveContainer width="100%" height={350}>
                                <BarChart
                                    data={data.chartData}
                                    layout="vertical"
                                    margin={{
                                        left: 0,
                                        right: 20,
                                        top: 20,
                                        bottom: 20,
                                    }}
                                >
                                    <CartesianGrid strokeDasharray="3 3" />
                                    <XAxis
                                        type="number"
                                        domain={[0, 'dataMax + 60']}
                                        tickFormatter={(value) => (value / 60).toFixed(1)}
                                    >
                                        <Label value={t('hours')} position="insideBottom" offset={-10} />
                                    </XAxis>
                                    <YAxis
                                        dataKey="name"
                                        type="category"
                                        tickLine={false}
                                        tickMargin={20}
                                        axisLine={false}
                                        width={160}
                                        tick={<CustomYAxisTick />}
                                    />
                                    <Tooltip content={<CustomTooltip />} />
                                    <Bar
                                        dataKey="minutes"
                                        fill="hsl(var(--primary) / 0.8)"
                                        name={t('hours')}
                                        radius={5}
                                        stroke="hsl(var(--primary))"
                                        strokeWidth={1}
                                    />
                                </BarChart>
                            </ResponsiveContainer>
                        </CardContent>
                    </Card>

                    {/* Data Table */}
                    <Card>
                        <CardHeader>
                            <CardTitle>
                                {t('detailed_mechanic_stats')}
                            </CardTitle>
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
                                            <TableRow
                                                key={index}
                                                className="cursor-pointer hover:bg-muted/50"
                                                onClick={() =>
                                                    handleMechanicClick(
                                                        row.mechanicId,
                                                    )
                                                }
                                            >
                                                <TableCell className="font-medium">
                                                    {row.mechanic}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    {formatMinutes(
                                                        row.totalMinutes,
                                                    )}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    {row.ordersCompleted}
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    {formatMinutes(
                                                        Math.round(
                                                            row.avgTimePerOrder,
                                                        ),
                                                    )}
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
                </>
            )}
        </div>
    );
}

function TeamPerformanceSkeleton() {
    return (
        <>
            <div className="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <Skeleton className="h-5 w-32" />
                        <Skeleton className="size-4" />
                    </CardHeader>
                    <CardContent>
                        <Skeleton className="h-8 w-16" />
                        <Skeleton className="mt-1 h-4 w-40" />
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <Skeleton className="h-5 w-36" />
                        <Skeleton className="size-4" />
                    </CardHeader>
                    <CardContent>
                        <Skeleton className="h-8 w-12" />
                        <Skeleton className="mt-1 h-4 w-40" />
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <Skeleton className="h-5 w-28" />
                        <Skeleton className="size-4" />
                    </CardHeader>
                    <CardContent>
                        <Skeleton className="h-8 w-10" />
                    </CardContent>
                </Card>
            </div>
            <Card>
                <CardHeader>
                    <Skeleton className="h-6 w-48" />
                </CardHeader>
                <CardContent>
                    <Skeleton className="h-[350px] w-full" />
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <Skeleton className="h-6 w-52" />
                </CardHeader>
                <CardContent>
                    <div className="space-y-2">
                        <Skeleton className="h-10 w-full" />
                        <Skeleton className="h-10 w-full" />
                        <Skeleton className="h-10 w-full" />
                    </div>
                </CardContent>
            </Card>
        </>
    );
}
