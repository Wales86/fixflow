import { DataTablePagination } from '@/components/common/data-table-pagination';
import { StatusBadge } from '@/components/status-badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { router } from '@inertiajs/react';
import { format } from 'date-fns';

interface RepairOrdersTableProps {
    repairOrders: PaginatedData<App.Dto.RepairOrder.RepairOrderData>;
}

export function RepairOrdersTable({ repairOrders }: RepairOrdersTableProps) {
    const handleRowClick = (repairOrderId: number) => {
        router.visit(`/repair-orders/${repairOrderId}`);
    };

    const formatDate = (date: string | null): string => {
        return date ? format(new Date(date), 'dd.MM.yyyy') : '—';
    };

    const formatMinutes = (minutes: number): string => {
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return `${hours}h ${mins}min`;
    };

    if (repairOrders.data.length === 0) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>Historia napraw</CardTitle>
                    <CardDescription>
                        Brak historii napraw dla tego pojazdu
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div className="flex flex-col items-center justify-center gap-4 py-8">
                        <p className="text-sm text-muted-foreground">
                            Ten pojazd nie ma jeszcze żadnych zleceń naprawy.
                        </p>
                    </div>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardHeader>
                <CardTitle>Historia napraw</CardTitle>
                <CardDescription>
                    Lista wszystkich zleceń naprawy dla tego pojazdu
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>ID</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Czas pracy</TableHead>
                            <TableHead>Data rozpoczęcia</TableHead>
                            <TableHead>Data zakończenia</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {repairOrders.data.map((order) => (
                            <TableRow
                                key={order.id}
                                onClick={() => handleRowClick(order.id)}
                                className="cursor-pointer"
                            >
                                <TableCell className="font-medium">
                                    #{order.id}
                                </TableCell>
                                <TableCell>
                                    <StatusBadge status={order.status} />
                                </TableCell>
                                <TableCell>
                                    {formatMinutes(order.total_time_minutes)}
                                </TableCell>
                                <TableCell>
                                    {formatDate(order.started_at)}
                                </TableCell>
                                <TableCell>
                                    {formatDate(order.finished_at)}
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
                <DataTablePagination pagination={repairOrders} />
            </CardContent>
        </Card>
    );
}
