import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Search } from 'lucide-react';
import { useMemo } from 'react';

import { DataTablePagination } from '@/components/common/data-table-pagination';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useDataTableFilters } from '@/hooks/use-data-table-filters';
import {
    type ColumnDef,
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';

import { DataTableRowActions } from './data-table-row-actions';

export function MechanicsDataTable({
    tableData,
    filters,
}: App.Dto.Common.FilterableTablePagePropsData) {
    const { t } = useLaravelReactI18n();
    const { search, handleSearch, handleSort, currentSort, currentDirection } =
        useDataTableFilters(filters);

    const mechanics: Array<App.Dto.Mechanic.MechanicData> = tableData.data;

    const columns = useMemo<ColumnDef<App.Dto.Mechanic.MechanicData>[]>(() => {
        return [
            {
                accessorKey: 'full_name',
                header: t('mechanics.full_name'),
                cell: ({ row }) => {
                    const firstName = row.original.first_name;
                    const lastName = row.original.last_name;
                    return `${firstName} ${lastName}`.trim();
                },
                enableSorting: true,
            },
            {
                accessorKey: 'is_active',
                header: t('mechanics.status'),
                cell: ({ row }) => {
                    const isActive = row.original.is_active;
                    return (
                        <Badge
                            variant={isActive ? 'default' : 'secondary'}
                            className={
                                isActive
                                    ? 'bg-green-500 hover:bg-green-600'
                                    : ''
                            }
                        >
                            {isActive
                                ? t('mechanics.active')
                                : t('mechanics.inactive')}
                        </Badge>
                    );
                },
                enableSorting: false,
            },
            {
                accessorKey: 'time_entries_count',
                header: t('mechanics.time_entries_count'),
                cell: ({ row }) => row.original.time_entries_count ?? 0,
                enableSorting: false,
            },
            {
                accessorKey: 'created_at',
                header: t('mechanics.created_at'),
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
                    <DataTableRowActions mechanic={row.original} />
                ),
            },
        ];
    }, [t]);

    const table = useReactTable({
        data: mechanics,
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
                        <CardTitle>{t('mechanics.list_title')}</CardTitle>
                        <CardDescription>
                            {t('mechanics.list_description')}
                        </CardDescription>
                    </div>
                    <div className="relative w-64">
                        <Search className="absolute top-2.5 left-2 size-4 text-muted-foreground" />
                        <Input
                            placeholder={t('mechanics.search_placeholder')}
                            value={search}
                            onChange={(e) => handleSearch(e.target.value)}
                            className="pl-8"
                        />
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
                                <TableRow key={row.id}>
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
                                    {search
                                        ? t('mechanics.no_results_found')
                                        : t('mechanics.no_mechanics_found')}
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
