import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';
import type { Table } from '@tanstack/react-table';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-react';

interface DataTablePaginationProps<TData> {
    table: Table<TData>;
    pagination: PaginatedResponse<TData>;
}

export function DataTablePagination<TData>({
    table,
    pagination,
}: DataTablePaginationProps<TData>) {
    const handlePageChange = (page: number) => {
        const url = new URL(window.location.href);
        url.searchParams.set('page', page.toString());
        router.get(url.pathname + url.search, {}, { preserveState: true, replace: true });
    };

    return (
        <div className="flex items-center justify-between px-2 py-4">
            <div className="text-sm text-muted-foreground">
                Wyświetlanie {pagination.from} do {pagination.to} z {pagination.total} wyników
            </div>
            <div className="flex items-center gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    onClick={() => handlePageChange(1)}
                    disabled={pagination.current_page === 1}
                >
                    <ChevronsLeft className="size-4" />
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    onClick={() => handlePageChange(pagination.current_page - 1)}
                    disabled={pagination.current_page === 1}
                >
                    <ChevronLeft className="size-4" />
                </Button>
                <div className="flex items-center gap-1 text-sm">
                    <span>Strona</span>
                    <strong>
                        {pagination.current_page} z {pagination.last_page}
                    </strong>
                </div>
                <Button
                    variant="outline"
                    size="sm"
                    onClick={() => handlePageChange(pagination.current_page + 1)}
                    disabled={pagination.current_page === pagination.last_page}
                >
                    <ChevronRight className="size-4" />
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    onClick={() => handlePageChange(pagination.last_page)}
                    disabled={pagination.current_page === pagination.last_page}
                >
                    <ChevronsRight className="size-4" />
                </Button>
            </div>
        </div>
    );
}
