import { router } from '@inertiajs/react';
import { useState } from 'react';
import { useDebouncedCallback } from 'use-debounce';

interface DataTableFilters {
    search: string | null;
    sort: string | null;
    direction: string | null;
}

export function useDataTableFilters(initialFilters: DataTableFilters) {
    const [search, setSearch] = useState(initialFilters.search || '');
    const [currentSort, setCurrentSort] = useState(initialFilters.sort);
    const [currentDirection, setCurrentDirection] = useState(
        initialFilters.direction,
    );

    const debouncedSearch = useDebouncedCallback((value: string) => {
        router.get(
            window.location.pathname,
            {
                search: value || undefined,
                sort: currentSort || undefined,
                direction: currentDirection || undefined,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 300);

    const handleSearch = (value: string) => {
        setSearch(value);
        debouncedSearch(value);
    };

    const handleSort = (columnId: string) => {
        let newDirection: 'asc' | 'desc' = 'asc';

        if (currentSort === columnId) {
            newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
        }

        setCurrentSort(columnId);
        setCurrentDirection(newDirection);

        router.get(
            window.location.pathname,
            {
                search: search || undefined,
                sort: columnId,
                direction: newDirection,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    return {
        search,
        handleSearch,
        handleSort,
        currentSort,
        currentDirection,
    };
}
