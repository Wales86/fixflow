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

export function UsersDataTable({
    tableData,
    filters,
}: App.Dto.Common.FilterableTablePagePropsData) {
    const { t } = useLaravelReactI18n();
    const { search, handleSearch, handleSort, currentSort, currentDirection } =
        useDataTableFilters(filters);

    const users: Array<App.Dto.User.UserData> = tableData.data;

    const columns = useMemo<ColumnDef<App.Dto.User.UserData>[]>(() => {
        return [
            {
                accessorKey: 'name',
                header: t('name'),
                cell: ({ row }) => row.original.name,
                enableSorting: true,
            },
            {
                accessorKey: 'email',
                header: t('email'),
                cell: ({ row }) => row.original.email,
                enableSorting: true,
            },
            {
                accessorKey: 'roles',
                header: t('users.roles'),
                cell: ({ row }) => {
                    const roles = row.original.roles;
                    return (
                        <div className="flex gap-1">
                            {roles.map((role) => (
                                <Badge key={role} variant="outline">
                                    {role}
                                </Badge>
                            ))}
                        </div>
                    );
                },
                enableSorting: false,
            },
            {
                accessorKey: 'created_at',
                header: t('users.created_at'),
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
                    <DataTableRowActions user={row.original} />
                ),
            },
        ];
    }, [t]);

    const table = useReactTable({
        data: users,
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
                        <CardTitle>{t('users.list_title')}</CardTitle>
                        <CardDescription>
                            {t('users.list_description')}
                        </CardDescription>
                    </div>
                    <div className="relative w-64">
                        <Search className="absolute top-2.5 left-2 size-4 text-muted-foreground" />
                        <Input
                            placeholder={t('users.search_placeholder')}
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
                                        ? t('users.no_results_found')
                                        : t('users.no_users_found')}
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
