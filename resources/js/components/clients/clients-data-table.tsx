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
import { Input } from '@/components/ui/input';
import { Search } from 'lucide-react';
import { DataTablePagination } from '@/components/common/data-table-pagination';
import { useDataTableFilters } from '@/hooks/use-data-table-filters';
import {
    type ColumnDef,
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { useMemo } from 'react';
import { DataTableRowActions } from './data-table-row-actions';
import { router } from '@inertiajs/react';

export function ClientsDataTable({ tableData, filters }: App.Dto.Common.FilterableTablePagePropsData) {
    const { search, handleSearch, handleSort, currentSort, currentDirection } =
        useDataTableFilters(filters);

    const handleRowClick = (clientId: number) => {
        router.visit(`/clients/${clientId}`);
    };

    const clients: Array<App.Dto.Client.ClientListItemData> = tableData.data;

    const columns = useMemo<
        ColumnDef<App.Dto.Client.ClientListItemData>[]
    >(() => {
        return [
            {
                accessorKey: 'full_name',
                header: 'Imię i nazwisko',
                cell: ({ row }) => {
                    const firstName = row.original.first_name;
                    const lastName = row.original.last_name || '';
                    return `${firstName} ${lastName}`.trim();
                },
                enableSorting: true,
            },
            {
                accessorKey: 'phone_number',
                header: 'Numer telefonu',
                cell: ({ row }) => row.original.phone_number,
                enableSorting: false,
            },
            {
                accessorKey: 'email',
                header: 'Email',
                cell: ({ row }) => row.original.email || '-',
                enableSorting: false,
            },
            {
                accessorKey: 'vehicles_count',
                header: 'Liczba pojazdów',
                cell: ({ row }) => row.original.vehicles_count,
                enableSorting: false,
            },
            {
                id: 'actions',
                cell: ({ row }) => <DataTableRowActions client={row.original} />,
            },
        ];
    }, []);

    const table = useReactTable({
        data: clients,
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
                        <CardTitle>Lista klientów</CardTitle>
                        <CardDescription>
                            Zarządzaj klientami warsztatu
                        </CardDescription>
                    </div>
                    <div className="relative w-64">
                        <Search className="absolute left-2 top-2.5 size-4 text-muted-foreground" />
                        <Input
                            placeholder="Szukaj klientów..."
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
                                                canSort ? 'cursor-pointer select-none' : ''
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
                                    {search
                                        ? 'Nie znaleziono klientów pasujących do Twoich kryteriów'
                                        : 'Nie znaleziono klientów'}
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
