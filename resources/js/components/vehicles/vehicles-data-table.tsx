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
import { router } from '@inertiajs/react';

export function VehiclesDataTable({ tableData, filters }: App.Dto.Common.FilterableTablePagePropsData) {
    const { search, handleSearch, handleSort, currentSort, currentDirection } =
        useDataTableFilters(filters);

    const handleRowClick = (vehicleId: number) => {
        router.visit(`/vehicles/${vehicleId}`);
    };

    const vehicles: Array<App.Dto.Vehicle.VehicleData> = tableData.data;

    const columns = useMemo<
        ColumnDef<App.Dto.Vehicle.VehicleData>[]
    >(() => {
        return [
            {
                accessorKey: 'registration_number',
                header: 'Nr rejestracyjny',
                cell: ({ row }) => row.original.registration_number,
                enableSorting: true,
            },
            {
                accessorKey: 'make',
                header: 'Marka',
                cell: ({ row }) => row.original.make,
                enableSorting: true,
            },
            {
                accessorKey: 'model',
                header: 'Model',
                cell: ({ row }) => row.original.model,
                enableSorting: true,
            },
            {
                accessorKey: 'year',
                header: 'Rocznik',
                cell: ({ row }) => row.original.year,
                enableSorting: true,
            },
            {
                accessorKey: 'vin',
                header: 'VIN',
                cell: ({ row }) => row.original.vin,
                enableSorting: false,
            },
            {
                accessorKey: 'client',
                header: 'Właściciel',
                cell: ({ row }) => {
                    const client = row.original.client;
                    if (!client) {
                        return '-';
                    }
                    const firstName = client.first_name;
                    const lastName = client.last_name || '';
                    return `${firstName} ${lastName}`.trim();
                },
                enableSorting: false,
            },
            {
                accessorKey: 'repair_orders_count',
                header: 'Liczba napraw',
                cell: ({ row }) => row.original.repair_orders_count ?? 0,
                enableSorting: false,
            },
        ];
    }, []);

    const table = useReactTable({
        data: vehicles,
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
                        <CardTitle>Lista pojazdów</CardTitle>
                        <CardDescription>
                            Zarządzaj pojazdami w warsztacie
                        </CardDescription>
                    </div>
                    <div className="relative w-64">
                        <Search className="absolute left-2 top-2.5 size-4 text-muted-foreground" />
                        <Input
                            placeholder="Szukaj pojazdów..."
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
                                    onClick={() => handleRowClick(row.original.id)}
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
                                        ? 'Nie znaleziono pojazdów pasujących do Twoich kryteriów'
                                        : 'Nie znaleziono pojazdów'}
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
                <DataTablePagination table={table} pagination={tableData} />
            </CardContent>
        </Card>
    );
}
