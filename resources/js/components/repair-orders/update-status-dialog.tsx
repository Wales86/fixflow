import { useForm } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface UpdateStatusDialogProps {
    order: App.Dto.RepairOrder.RepairOrderShowData;
    isOpen: boolean;
    onClose: () => void;
}

const statusOptions: { value: App.Enums.RepairOrderStatus; label: string }[] = [
    { value: 'new', label: 'Nowe' },
    { value: 'diagnosis', label: 'Diagnoza' },
    { value: 'awaiting_contact', label: 'Wymaga kontaktu' },
    { value: 'awaiting_parts', label: 'Czeka na części' },
    { value: 'in_progress', label: 'W naprawie' },
    { value: 'ready_for_pickup', label: 'Gotowe do odbioru' },
    { value: 'closed', label: 'Zamknięte' },
];

export function UpdateStatusDialog({
    order,
    isOpen,
    onClose,
}: UpdateStatusDialogProps) {
    const { t } = useLaravelReactI18n();

    const { data, setData, patch, processing, reset } =
        useForm<App.Dto.RepairOrder.UpdateRepairOrderStatusData>({
            status: order.status,
        });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        patch(`/repair-orders/${order.id}/status`, {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                onClose();
            },
        });
    };

    const handleOpenChange = (open: boolean) => {
        if (!open) {
            reset();
            onClose();
        }
    };

    return (
        <Dialog open={isOpen} onOpenChange={handleOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{t('change_status')}</DialogTitle>
                    <DialogDescription>
                        {t('change_status_description')}
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit}>
                    <div className="space-y-4 py-4">
                        <div className="space-y-2">
                            <Label htmlFor="status">{t('status')}</Label>
                            <Select
                                value={data.status}
                                onValueChange={(value) =>
                                    setData(
                                        'status',
                                        value as App.Enums.RepairOrderStatus,
                                    )
                                }
                            >
                                <SelectTrigger id="status">
                                    <SelectValue
                                        placeholder={t('select_status')}
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    {statusOptions.map((option) => (
                                        <SelectItem
                                            key={option.value}
                                            value={option.value}
                                        >
                                            {option.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => handleOpenChange(false)}
                            disabled={processing}
                        >
                            {t('cancel')}
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? t('saving') : t('save')}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
