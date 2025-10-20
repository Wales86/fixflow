import { Head } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { useState } from 'react';

import { AddNoteDialog } from '@/components/internal-notes/add-note-dialog';
import { RepairOrderDetailsCard } from '@/components/repair-orders/repair-order-details-card';
import { RepairOrderHeader } from '@/components/repair-orders/repair-order-header';
import { RepairOrderTabs } from '@/components/repair-orders/repair-order-tabs';
import { UpdateStatusDialog } from '@/components/repair-orders/update-status-dialog';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';

export default function RepairOrderShow({
    order,
    time_entries,
    internal_notes,
    activity_log,
    can_edit,
    can_delete,
}: App.Dto.RepairOrder.RepairOrderShowPagePropsData) {
    const { t } = useLaravelReactI18n();
    const [isStatusDialogOpen, setStatusDialogOpen] = useState(false);
    const [isNoteDialogOpen, setNoteDialogOpen] = useState(false);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('repair_orders'),
            href: '/repair-orders',
        },
        {
            title: `#${order.id}`,
            href: `/repair-orders/${order.id}`,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${t('repair_order')} #${order.id}`} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <RepairOrderHeader
                    order={order}
                    can_edit={can_edit}
                    can_delete={can_delete}
                    onStatusChange={() => setStatusDialogOpen(true)}
                    onAddNote={() => setNoteDialogOpen(true)}
                />

                <RepairOrderDetailsCard order={order} />

                <RepairOrderTabs
                    time_entries={time_entries}
                    internal_notes={internal_notes}
                    activity_log={activity_log}
                />
            </div>

            <UpdateStatusDialog
                order={order}
                isOpen={isStatusDialogOpen}
                onClose={() => setStatusDialogOpen(false)}
            />

            <AddNoteDialog
                repairOrderId={order.id}
                isOpen={isNoteDialogOpen}
                onClose={() => setNoteDialogOpen(false)}
            />
        </AppLayout>
    );
}
