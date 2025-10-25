import { useForm, usePage } from '@inertiajs/react';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { type SharedData } from '@/types';

interface TimeEntryDialogProps {
    isOpen: boolean;
    onClose: () => void;
    repairOrderId: number;
    timeEntry?: App.Dto.TimeTracking.TimeEntryData | null;
}

export function TimeEntryDialog({
    isOpen,
    onClose,
    repairOrderId,
    timeEntry,
}: TimeEntryDialogProps) {
    const { t } = useLaravelReactI18n();
    const { mechanics } = usePage<SharedData>().props as any;

    const isEditMode = !!timeEntry;

    // Calculate initial hours and minutes from duration_minutes
    const initialHours = timeEntry
        ? Math.floor(timeEntry.duration_minutes / 60)
        : 0;
    const initialMinutes = timeEntry ? timeEntry.duration_minutes % 60 : 0;

    const { data, setData, post, patch, processing, errors, reset } = useForm({
        repair_order_id: repairOrderId,
        mechanic_id: timeEntry?.mechanic_id ?? '',
        duration_hours_input: initialHours,
        duration_minutes_input: initialMinutes,
        description: timeEntry?.description ?? '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        const options = {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                onClose();
            },
        };

        if (isEditMode && timeEntry) {
            patch(`/time-entries/${timeEntry.id}`, options);
        } else {
            post('/time-entries', options);
        }
    };

    const handleOpenChange = (open: boolean) => {
        if (!open) {
            reset();
            onClose();
        }
    };

    if (!mechanics || mechanics.length === 0) {
        return (
            <Dialog open={isOpen} onOpenChange={handleOpenChange}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{t('add_time_entry')}</DialogTitle>
                        <DialogDescription>
                            {t('no_mechanics_available')}
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button onClick={() => handleOpenChange(false)}>
                            {t('close')}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        );
    }

    return (
        <Dialog open={isOpen} onOpenChange={handleOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>
                        {isEditMode
                            ? t('edit_time_entry')
                            : t('add_time_entry')}
                    </DialogTitle>
                    <DialogDescription>
                        {isEditMode
                            ? t('edit_time_entry_description')
                            : t('add_time_entry_description')}
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit}>
                    <div className="space-y-4 py-4">
                        {/* Mechanic Select */}
                        <div className="space-y-2">
                            <Label htmlFor="mechanic_id">
                                {t('select_mechanic')}
                            </Label>
                            <Select
                                value={data.mechanic_id.toString()}
                                onValueChange={(value) =>
                                    setData('mechanic_id', parseInt(value))
                                }
                            >
                                <SelectTrigger>
                                    <SelectValue
                                        placeholder={t('select_mechanic')}
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    {mechanics?.map((mechanic: any) => (
                                        <SelectItem
                                            key={mechanic.id}
                                            value={mechanic.id.toString()}
                                        >
                                            {mechanic.full_name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.mechanic_id && (
                                <p className="text-sm text-red-500">
                                    {errors.mechanic_id}
                                </p>
                            )}
                        </div>

                        {/* Duration Inputs */}
                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-2">
                                <Label htmlFor="duration_hours">
                                    {t('hours')}
                                </Label>
                                <Input
                                    id="duration_hours"
                                    type="number"
                                    min="0"
                                    max="24"
                                    value={data.duration_hours_input}
                                    onChange={(e) =>
                                        setData(
                                            'duration_hours_input',
                                            parseInt(e.target.value) || 0,
                                        )
                                    }
                                />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="duration_minutes_input">
                                    {t('minutes')}
                                </Label>
                                <Input
                                    id="duration_minutes_input"
                                    type="number"
                                    min="0"
                                    max="59"
                                    value={data.duration_minutes_input}
                                    onChange={(e) =>
                                        setData(
                                            'duration_minutes_input',
                                            parseInt(e.target.value) || 0,
                                        )
                                    }
                                />
                            </div>
                        </div>
                        {errors.duration_hours_input && (
                            <p className="text-sm text-red-500">
                                {errors.duration_hours_input}
                            </p>
                        )}
                        {errors.duration_minutes_input && (
                            <p className="text-sm text-red-500">
                                {errors.duration_minutes_input}
                            </p>
                        )}

                        {/* Description */}
                        <div className="space-y-2">
                            <Label htmlFor="description">
                                {t('work_description')}
                            </Label>
                            <Textarea
                                id="description"
                                placeholder={t('work_description_placeholder')}
                                value={data.description}
                                onChange={(e) =>
                                    setData('description', e.target.value)
                                }
                                rows={4}
                            />
                            {errors.description && (
                                <p className="text-sm text-red-500">
                                    {errors.description}
                                </p>
                            )}
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
