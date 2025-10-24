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
import { usePermission } from '@/lib/permissions';
import { Clock, Edit, FileText, MoreVertical, RefreshCw, Trash2 } from 'lucide-react';

interface RepairOrderHeaderProps {
    order: App.Dto.RepairOrder.RepairOrderShowData;
    onStatusChange: () => void;
    onAddNote: () => void;
    onAddTimeEntry?: () => void;
}

export function RepairOrderHeader({
    order,
    onStatusChange,
    onAddNote,
    onAddTimeEntry,
}: RepairOrderHeaderProps) {
    const { t } = useLaravelReactI18n();
    const canUpdateStatus = usePermission('update_repair_order_status');
    const canCreateNotes = usePermission('create_internal_notes');
    const canCreateTimeEntry = usePermission('create_time_entries');
    const canEdit = usePermission('update_repair_orders');
    const canDelete = usePermission('delete_repair_orders');

    const handleEdit = () => {
        router.visit(`/repair-orders/${order.id}/edit`);
    };

    const handleDelete = () => {
        if (confirm(t('confirm_delete_repair_order'))) {
            router.delete(`/repair-orders/${order.id}`);
        }
    };

    const hasAnyAction =
        canUpdateStatus ||
        canCreateNotes ||
        canCreateTimeEntry ||
        canEdit ||
        canDelete;

    return (
        <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
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

            {hasAnyAction && (
                <div className="flex flex-wrap gap-2">
                    {canUpdateStatus && (
                        <Button variant="outline" onClick={onStatusChange}>
                            <RefreshCw className="mr-2 h-4 w-4" />
                            {t('change_status')}
                        </Button>
                    )}

                    {canCreateNotes && (
                        <Button variant="outline" onClick={onAddNote}>
                            <FileText className="mr-2 h-4 w-4" />
                            {t('add_note')}
                        </Button>
                    )}

                    {canCreateTimeEntry && onAddTimeEntry && (
                        <Button variant="outline" onClick={onAddTimeEntry}>
                            <Clock className="mr-2 h-4 w-4" />
                            {t('add_time_entry')}
                        </Button>
                    )}

                    {(canEdit || canDelete) && (
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="outline" size="icon">
                                    <MoreVertical className="h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                {canEdit && (
                                    <DropdownMenuItem onClick={handleEdit}>
                                        <Edit className="mr-2 h-4 w-4" />
                                        {t('edit')}
                                    </DropdownMenuItem>
                                )}
                                {canDelete && (
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
                    )}
                </div>
            )}
        </div>
    );
}
