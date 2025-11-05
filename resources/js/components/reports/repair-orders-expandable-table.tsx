import { StatusBadge } from '@/components/status-badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatMinutes } from '@/lib/utils';
import { format } from 'date-fns';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { ChevronDown, ChevronUp } from 'lucide-react';
import React, { useState } from 'react';

interface RepairOrdersExpandableTableProps {
    orders: App.Dto.Report.RepairOrderDetailData[];
}

export function RepairOrdersExpandableTable({
    orders,
}: RepairOrdersExpandableTableProps) {
    const { t } = useLaravelReactI18n();
    const [expandedRows, setExpandedRows] = useState<Set<number>>(new Set());

    const toggleRow = (orderId: number) => {
        setExpandedRows((prev) => {
            const next = new Set(prev);
            if (next.has(orderId)) {
                next.delete(orderId);
            } else {
                next.add(orderId);
            }
            return next;
        });
    };

    const formatDate = (dateString: string | null): string => {
        if (!dateString) return '-';
        return format(new Date(dateString), 'dd.MM.yyyy');
    };

    const formatDateTime = (dateString: string): string => {
        return format(new Date(dateString), 'dd.MM.yyyy HH:mm');
    };

    if (orders.length === 0) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>{t('repair_orders')}</CardTitle>
                </CardHeader>
                <CardContent>
                    <p className="text-center text-muted-foreground">
                        {t('no_repair_orders_for_period')}
                    </p>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardHeader>
                <CardTitle>{t('repair_orders_details')}</CardTitle>
            </CardHeader>
            <CardContent>
                <div className="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[40px]"></TableHead>
                                <TableHead>{t('vehicle')}</TableHead>
                                <TableHead>{t('status')}</TableHead>
                                <TableHead>{t('started_at')}</TableHead>
                                <TableHead>{t('finished_at')}</TableHead>
                                <TableHead className="text-right">
                                    {t('total_time')}
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {orders.map((order) => {
                                const isExpanded = expandedRows.has(order.id);
                                return (
                                    <React.Fragment key={order.id}>
                                        <TableRow className="group">
                                            <TableCell>
                                                <button
                                                    className="flex items-center justify-center rounded p-1 hover:bg-muted"
                                                    onClick={() => toggleRow(order.id)}
                                                >
                                                    {isExpanded ? (
                                                        <ChevronUp className="h-4 w-4" />
                                                    ) : (
                                                        <ChevronDown className="h-4 w-4" />
                                                    )}
                                                </button>
                                            </TableCell>
                                            <TableCell className="font-medium">
                                                {order.vehicleInfo}
                                            </TableCell>
                                            <TableCell>
                                                <StatusBadge status={order.status as App.Enums.RepairOrderStatus} />
                                            </TableCell>
                                            <TableCell>
                                                {formatDate(order.startedAt)}
                                            </TableCell>
                                            <TableCell>
                                                {formatDate(order.finishedAt)}
                                            </TableCell>
                                            <TableCell className="text-right font-medium">
                                                {formatMinutes(order.totalMinutes)}
                                            </TableCell>
                                        </TableRow>
                                        {isExpanded && (
                                            <TableRow>
                                                <TableCell colSpan={6} className="bg-muted/30 p-0">
                                                    <div className="p-4">
                                                        <h4 className="mb-3 text-sm font-semibold">{t('time_entries')}</h4>
                                                        <Table>
                                                            <TableHeader>
                                                                <TableRow>
                                                                    <TableHead>{t('date')}</TableHead>
                                                                    <TableHead>{t('duration')}</TableHead>
                                                                    <TableHead>{t('description')}</TableHead>
                                                                </TableRow>
                                                            </TableHeader>
                                                            <TableBody>
                                                                {order.timeEntries.length >0 ? (
                                                                    order.timeEntries.map((entry) => (
                                                                            <TableRow key={entry.id}>
                                                                                <TableCell>
                                                                                    {formatDateTime(entry.date)}
                                                                                </TableCell>
                                                                                <TableCell>
                                                                                    {formatMinutes(entry.durationMinutes)}
                                                                                </TableCell>
                                                                                <TableCell>{entry.description || '-'}</TableCell>
                                                                            </TableRow>
                                                                        ))
                                                                ) : (
                                                                    <TableRow>
                                                                        <TableCell colSpan={3} className="text-center text-muted-foreground">{t('no_time_entries')}</TableCell>
                                                                    </TableRow>
                                                                )}
                                                            </TableBody>
                                                        </Table>
                                                    </div>
                                                </TableCell>
                                            </TableRow>
                                        )}
                                    </React.Fragment>
                                );
                            })}
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    );
}
