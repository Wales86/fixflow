import { DataTablePagination } from '@/components/common/data-table-pagination';
import { StatusBadge } from '@/components/status-badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useDataTableFilters } from '@/hooks/use-data-table-filters';
import { router } from '@inertiajs/react';
import {
    type ColumnDef,
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Search } from 'lucide-react';
import { useMemo } from 'react';
import { RepairOrdersTableActions } from './repair-orders-table-actions';

interface RepairOrdersDataTableProps {
    tableData: PaginatedData<App.Dto.RepairOrder.RepairOrderListItemData>;
    filters: App.Dto.Common.FiltersData & { status?: string | null };
    statusOptions: Array<App.Dto.Common.SelectOptionData>;
}

export function RepairOrdersDataTable({
    tableData,
    filters,
    statusOptions,
}: RepairOrdersDataTableProps) {
    const { t } = useLaravelReactI18n();
    const {
        search,
        handleSearch,
        handleSort,
        currentSort,
        currentDirection,
        additionalFilters,
        handleFilterChange,
    } = useDataTableFilters({
        ...filters,
        status: filters.status || null,
    });

    const handleRowClick = (orderId: number) => {
        router.visit(`/repair-orders/${orderId}`);
    };

    const orders: Array<App.Dto.RepairOrder.RepairOrderListItemData> =
        tableData.data;

    const columns = useMemo<
        ColumnDef<App.Dto.RepairOrder.RepairOrderListItemData>[]
    >(() => {
        return [
            {
                accessorKey: 'id',
                header: t('order_number'),
                cell: ({ row }) => `#${row.original.id}`,
                enableSorting: true,
            },
            {
                accessorKey: 'vehicle',
                header: t('vehicle'),
                cell: ({ row }) => {
                    const vehicle = row.original.vehicle;
                    return (
                        <div className="flex flex-col">
                            <span className="font-medium">
                                {vehicle.make} {vehicle.model}
                            </span>
                            <span className="text-sm text-muted-foreground">
                                {vehicle.registration_number}
                            </span>
                        </div>
                    );
                },
                enableSorting: false,
            },
            {
                accessorKey: 'client',
                header: t('client'),
                cell: ({ row }) => {
                    const client = row.original.client;
                    const fullName =
                        `${client.first_name} ${client.last_name || ''}`.trim();
                    return (
                        <div className="flex flex-col">
                            <span className="font-medium">{fullName}</span>
                            <span className="text-sm text-muted-foreground">
                                {client.phone_number}
                            </span>
                        </div>
                    );
                },
                enableSorting: false,
            },
            {
                accessorKey: 'status',
                header: t('status'),
                cell: ({ row }) => <StatusBadge status={row.original.status} />,
                enableSorting: true,
            },
            {
                accessorKey: 'problem_description',
                header: t('problem_description'),
                cell: ({ row }) => {
                    const description = row.original.problem_description;
                    return (
                        <div className="max-w-md truncate" title={description}>
                            {description}
                        </div>
                    );
                },
                enableSorting: false,
            },
            {
                accessorKey: 'total_time_minutes',
                header: t('work_time'),
                cell: ({ row }) => {
                    const minutes = row.original.total_time_minutes;
                    if (minutes === 0) {
                        return <span className="text-muted-foreground">-</span>;
                    }
                    const hours = Math.floor(minutes / 60);
                    const mins = minutes % 60;
                    return `${hours}h ${mins}m`;
                },
                enableSorting: true,
            },
            {
                accessorKey: 'created_at',
                header: t('created_date'),
                cell: ({ row }) => {
                    const date = new Date(row.original.created_at);
                    return date.toLocaleDateString('pl-PL', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                    });
                },
                enableSorting: true,
            },
            {
                id: 'actions',
                cell: ({ row }) => (
                    <RepairOrdersTableActions order={row.original} />
                ),
            },
        ];
    }, [t]);

    const table = useReactTable({
        data: orders,
        columns,
        getCoreRowModel: getCoreRowModel(),
        manualSorting: true,
        manualPagination: true,
        pageCount: tableData.last_page,
    });

    return (
        <Card>
            <CardHeader>
                <div className="flex items-center justify-between">
                    <div>
                        <CardTitle>{t('repair_order_list')}</CardTitle>
                        <CardDescription>
                            {t('manage_repair_orders')}
                        </CardDescription>
                    </div>
                    <div className="flex gap-2">
                        <Select
                            value={additionalFilters.status || 'all'}
                            onValueChange={(value) =>
                                handleFilterChange(
                                    'status',
                                    value === 'all' ? null : value,
                                )
                            }
                        >
                            <SelectTrigger className="w-48">
                                <SelectValue placeholder={t('all_statuses')} />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">
                                    {t('all_statuses')}
                                </SelectItem>
                                {statusOptions.map((option) => (
                                    <SelectItem
                                        key={option.value}
                                        value={option.value}
                                    >
                                        {option.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <div className="relative w-64">
                            <Search className="absolute top-2.5 left-2 size-4 text-muted-foreground" />
                            <Input
                                placeholder={t('search_repair_orders')}
                                value={search}
                                onChange={(e) => handleSearch(e.target.value)}
                                className="pl-8"
                            />
                        </div>
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <TableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header) => {
                                    const canSort = header.column.getCanSort();
                                    return (
                                        <TableHead
                                            key={header.id}
                                            onClick={
                                                canSort
                                                    ? () =>
                                                          handleSort(
                                                              header.column.id,
                                                          )
                                                    : undefined
                                            }
                                            className={
                                                canSort
                                                    ? 'cursor-pointer select-none'
                                                    : ''
                                            }
                                        >
                                            {header.isPlaceholder
                                                ? null
                                                : flexRender(
                                                      header.column.columnDef
                                                          .header,
                                                      header.getContext(),
                                                  )}
                                            {canSort &&
                                                currentSort ===
                                                    header.column.id && (
                                                    <span className="ml-1">
                                                        {currentDirection ===
                                                        'asc'
                                                            ? '↑'
                                                            : '↓'}
                                                    </span>
                                                )}
                                        </TableHead>
                                    );
                                })}
                            </TableRow>
                        ))}
                    </TableHeader>
                    <TableBody>
                        {table.getRowModel().rows.length > 0 ? (
                            table.getRowModel().rows.map((row) => (
                                <TableRow
                                    key={row.id}
                                    onClick={(e) => {
                                        const target = e.target as HTMLElement;
                                        if (
                                            !target.closest('button') &&
                                            !target.closest('[role="menu"]')
                                        ) {
                                            handleRowClick(row.original.id);
                                        }
                                    }}
                                    className="cursor-pointer"
                                >
                                    {row.getVisibleCells().map((cell) => (
                                        <TableCell key={cell.id}>
                                            {flexRender(
                                                cell.column.columnDef.cell,
                                                cell.getContext(),
                                            )}
                                        </TableCell>
                                    ))}
                                </TableRow>
                            ))
                        ) : (
                            <TableRow>
                                <TableCell
                                    colSpan={columns.length}
                                    className="h-24 text-center"
                                >
                                    {search || additionalFilters.status
                                        ? t(
                                              'no_repair_orders_matching_criteria',
                                          )
                                        : t('no_repair_orders_found')}
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
                <DataTablePagination pagination={tableData} />
            </CardContent>
        </Card>
    );
}
