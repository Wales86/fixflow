import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { router } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Eye, MoreHorizontal, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface RepairOrdersTableActionsProps {
    order: App.Dto.RepairOrder.RepairOrderListItemData;
}

export function RepairOrdersTableActions({
    order,
}: RepairOrdersTableActionsProps) {
    const { t } = useLaravelReactI18n();
    const [showDeleteDialog, setShowDeleteDialog] = useState(false);

    const handleView = () => {
        router.visit(`/repair-orders/${order.id}`);
    };

    const handleEdit = () => {
        router.visit(`/repair-orders/${order.id}/edit`);
    };

    const handleDelete = () => {
        router.delete(`/repair-orders/${order.id}`, {
            onSuccess: () => {
                setShowDeleteDialog(false);
            },
        });
    };

    return (
        <>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="sm" className="size-8 p-0">
                        <span className="sr-only">{t('open_menu')}</span>
                        <MoreHorizontal className="size-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuLabel>{t('actions')}</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem onClick={handleView}>
                        <Eye className="mr-2 size-4" />
                        {t('see')}
                    </DropdownMenuItem>
                    <DropdownMenuItem onClick={handleEdit}>
                        <Pencil className="mr-2 size-4" />
                        {t('edit')}
                    </DropdownMenuItem>
                    <DropdownMenuItem
                        onClick={() => setShowDeleteDialog(true)}
                        className="text-destructive"
                    >
                        <Trash2 className="mr-2 size-4" />
                        {t('delete')}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <Dialog open={showDeleteDialog} onOpenChange={setShowDeleteDialog}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {t('delete_repair_order_title')}
                        </DialogTitle>
                        <DialogDescription>
                            {t('delete_repair_order_description')}{' '}
                            <strong>#{order.id}</strong>{' '}
                            {t('delete_repair_order_vehicle_info')}{' '}
                            <strong>
                                {order.vehicle.make} {order.vehicle.model} (
                                {order.vehicle.registration_number})
                            </strong>
                            .
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setShowDeleteDialog(false)}
                        >
                            {t('cancel')}
                        </Button>
                        <Button variant="destructive" onClick={handleDelete}>
                            {t('delete')}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}
