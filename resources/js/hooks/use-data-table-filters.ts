import { router } from '@inertiajs/react';
import { useState } from 'react';
import { useDebouncedCallback } from 'use-debounce';

interface DataTableFilters {
    search: string | null;
    sort: string | null;
    direction: string | null;
    [key: string]: string | null;
}

function buildQueryParams(
    search: string,
    sort: string | null,
    direction: string | null,
    additionalFilters: Record<string, string | null>
): Record<string, string> {
    const params: Record<string, string> = {};

    if (search) {
        params.search = search;
    }

    if (sort) {
        params.sort = sort;
    }

    if (direction) {
        params.direction = direction;
    }

    for (const [key, value] of Object.entries(additionalFilters)) {
        if (value) {
            params[key] = value;
        }
    }

    return params;
}

function extractAdditionalFilters(filters: DataTableFilters): Record<string, string | null> {
    const additionalFilters: Record<string, string | null> = {};

    for (const [key, value] of Object.entries(filters)) {
        if (key !== 'search' && key !== 'sort' && key !== 'direction') {
            additionalFilters[key] = value;
        }
    }

    return additionalFilters;
}

export function useDataTableFilters(initialFilters: DataTableFilters) {
    const [search, setSearch] = useState(initialFilters.search || '');
    const [currentSort, setCurrentSort] = useState(initialFilters.sort);
    const [currentDirection, setCurrentDirection] = useState(
        initialFilters.direction,
    );
    const [additionalFilters, setAdditionalFilters] = useState<Record<string, string | null>>(
        extractAdditionalFilters(initialFilters)
    );

    const debouncedSearch = useDebouncedCallback((searchValue: string) => {
        const params = buildQueryParams(searchValue, currentSort, currentDirection, additionalFilters);

        router.get(window.location.pathname, params, {
            preserveState: true,
            replace: true,
        });
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

        const params = buildQueryParams(search, columnId, newDirection, additionalFilters);

        router.get(window.location.pathname, params, {
            preserveState: true,
            replace: true,
        });
    };

    const handleFilterChange = (key: string, value: string | null) => {
        const updatedFilters = {
            ...additionalFilters,
            [key]: value,
        };

        setAdditionalFilters(updatedFilters);

        const params = buildQueryParams(search, currentSort, currentDirection, updatedFilters);

        router.get(window.location.pathname, params, {
            preserveState: true,
            replace: true,
        });
    };

    return {
        search,
        handleSearch,
        handleSort,
        currentSort,
        currentDirection,
        additionalFilters,
        handleFilterChange,
    };
}
