import { router } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Edit, FileText, MoreVertical, RefreshCw, Trash2 } from 'lucide-react';

interface RepairOrderHeaderProps {
    order: App.Dto.RepairOrder.RepairOrderShowData;
    can_edit: boolean;
    can_delete: boolean;
    onStatusChange: () => void;
    onAddNote: () => void;
}

export function RepairOrderHeader({
    order,
    can_edit,
    can_delete,
    onStatusChange,
    onAddNote,
}: RepairOrderHeaderProps) {
    const { t } = useLaravelReactI18n();

    const handleEdit = () => {
        router.visit(`/repair-orders/${order.id}/edit`);
    };

    const handleDelete = () => {
        if (confirm(t('confirm_delete_repair_order'))) {
            router.delete(`/repair-orders/${order.id}`);
        }
    };

    return (
        <div className="flex items-start justify-between">
            <div className="space-y-1">
                <div className="flex items-center gap-3">
                    <h1 className="text-2xl font-bold tracking-tight">
                        {t('repair_order')} #{order.id}
                    </h1>
                    <StatusBadge status={order.status} />
                </div>
                <p className="text-muted-foreground">
                    {order.vehicle.make} {order.vehicle.model} (
                    {order.vehicle.registration_number})
                </p>
            </div>

            {can_edit && (
                <div className="flex gap-2">
                    <Button variant="outline" onClick={onStatusChange}>
                        <RefreshCw className="mr-2 h-4 w-4" />
                        {t('change_status')}
                    </Button>

                    <Button variant="outline" onClick={onAddNote}>
                        <FileText className="mr-2 h-4 w-4" />
                        {t('add_note')}
                    </Button>

                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="outline" size="icon">
                                <MoreVertical className="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem onClick={handleEdit}>
                                <Edit className="mr-2 h-4 w-4" />
                                {t('edit')}
                            </DropdownMenuItem>
                            {can_delete && (
                                <DropdownMenuItem
                                    onClick={handleDelete}
                                    className="text-red-600"
                                >
                                    <Trash2 className="mr-2 h-4 w-4" />
                                    {t('delete')}
                                </DropdownMenuItem>
                            )}
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            )}
        </div>
    );
}
