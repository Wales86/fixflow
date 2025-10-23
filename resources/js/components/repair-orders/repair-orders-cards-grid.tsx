import { RepairOrderCard } from '@/components/repair-orders/repair-order-card';
import { Input } from '@/components/ui/input';
import { router } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Search } from 'lucide-react';
import { useDebouncedCallback } from 'use-debounce';

interface RepairOrdersCardsGridProps {
    orders: App.Dto.RepairOrder.MechanicRepairOrderCardData[];
    search?: string | null;
}

export function RepairOrdersCardsGrid({
    orders,
    search,
}: RepairOrdersCardsGridProps) {
    const { t } = useLaravelReactI18n();

    const handleSearch = useDebouncedCallback((value: string) => {
        router.get(
            '/repair-orders/mechanic',
            { search: value || undefined },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    }, 300);

    return (
        <div className="flex flex-col gap-4">
            {/* Search input */}
            <div className="relative">
                <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    type="search"
                    placeholder={t('search')}
                    defaultValue={search ?? ''}
                    onChange={(e) => handleSearch(e.target.value)}
                    className="pl-9"
                />
            </div>

            {/* Orders grid */}
            {orders.length > 0 ? (
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {orders.map((order) => (
                        <RepairOrderCard key={order.id} order={order} />
                    ))}
                </div>
            ) : (
                <div className="flex min-h-[400px] items-center justify-center">
                    <p className="text-muted-foreground">
                        {t('no_active_repair_orders')}
                    </p>
                </div>
            )}
        </div>
    );
}
