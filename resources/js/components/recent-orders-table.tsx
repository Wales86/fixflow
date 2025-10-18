import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { StatusBadge } from '@/components/status-badge';
import { format } from 'date-fns';

interface RecentOrdersTableProps {
    orders: App.Dto.Dashboard.RecentOrderData[];
}

export function RecentOrdersTable({ orders }: RecentOrdersTableProps) {
    return (
        <Card>
            <CardHeader>
                <CardTitle>Ostatnie zlecenia</CardTitle>
                <CardDescription>
                    Lista ostatnio aktualizowanych zleceń w warsztacie.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Pojazd</TableHead>
                            <TableHead>Klient</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Data utworzenia</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {orders.length > 0 ? (
                            orders.map((order) => (
                                <TableRow key={order.id}>
                                    <TableCell className="font-medium">
                                        {order.vehicle}
                                    </TableCell>
                                    <TableCell>{order.client}</TableCell>
                                    <TableCell>
                                        <StatusBadge
                                            status={
                                                order.status as App.Enums.RepairOrderStatus
                                            }
                                        />
                                    </TableCell>
                                    <TableCell>
                                        {format(
                                            new Date(order.created_at),
                                            'yyyy-MM-dd',
                                        )}
                                    </TableCell>
                                </TableRow>
                            ))
                        ) : (
                            <TableRow>
                                <TableCell
                                    colSpan={4}
                                    className="text-center"
                                >
                                    Brak ostatnich zleceń do wyświetlenia.
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    );
}
