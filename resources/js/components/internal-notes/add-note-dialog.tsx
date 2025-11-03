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

interface AddNoteDialogProps {
    notableType: App.Enums.NotableType;
    notableId: number;
    isOpen: boolean;
    onClose: () => void;
}

export function AddNoteDialog({
    notableType,
    notableId,
    isOpen,
    onClose,
}: AddNoteDialogProps) {
    const { t } = useLaravelReactI18n();
    const { mechanics } = usePage<SharedData>().props;
    const { auth: { user } } = usePage<SharedData>().props;

    const isMechanic = user?.roles.includes('Mechanic');

    const { data, setData, post, processing, errors, reset } = useForm({
        notable_type: notableType,
        notable_id: notableId,
        content: '',
        mechanic_id: null as number | null,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        post('/internal-notes', {
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
                    <DialogTitle>{t('add_internal_note')}</DialogTitle>
                    <DialogDescription>
                        {t('add_internal_note_description')}
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit}>
                    <div className="space-y-4 py-4">
                        {isMechanic && mechanics && mechanics.length > 0 && (
                            <div className="space-y-2">
                                <Label htmlFor="mechanic_id">
                                    {t('select_mechanic')}
                                </Label>
                                <Select
                                    value={data.mechanic_id?.toString() ?? ''}
                                    onValueChange={(value) =>
                                        setData(
                                            'mechanic_id',
                                            value ? parseInt(value) : null,
                                        )
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue
                                            placeholder={t('select_mechanic')}
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {mechanics?.map((mechanic) => (
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
                        )}

                        <div className="space-y-2">
                            <Label htmlFor="content">{t('note_content')}</Label>
                            <Textarea
                                id="content"
                                placeholder={t('note_content_placeholder')}
                                value={data.content}
                                onChange={(e) =>
                                    setData('content', e.target.value)
                                }
                                rows={6}
                                className={
                                    errors.content ? 'border-red-500' : ''
                                }
                            />
                            {errors.content && (
                                <p className="text-sm text-red-500">
                                    {errors.content}
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
