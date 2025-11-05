import { useForm } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { FormEventHandler } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { store, update } from '@/routes/mechanics';
import { toast } from 'sonner';

interface MechanicFormProps {
    mechanic?: App.Dto.Mechanic.MechanicData;
}

type MechanicFormData = {
    first_name: string;
    last_name: string;
    is_active: boolean;
};

export function MechanicForm({ mechanic }: MechanicFormProps) {
    const { t } = useLaravelReactI18n();
    const isEditMode = !!mechanic;

    const { data, setData, post, put, processing, errors, clearErrors } =
        useForm<MechanicFormData>({
            first_name: mechanic?.first_name ?? '',
            last_name: mechanic?.last_name ?? '',
            is_active: mechanic?.is_active ?? true,
        });

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEditMode) {
            put(update(mechanic.id).url, {
                preserveScroll: true,
                onError: () => {
                    toast.error(t('an_error_occurred'));
                },
            });
        } else {
            post(store().url, {
                preserveScroll: true,
                onError: () => {
                    toast.error(t('an_error_occurred'));
                },
            });
        }
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>
                    {isEditMode
                        ? t('mechanics.edit_mechanic_data')
                        : t('mechanics.mechanic_data')}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid gap-6 md:grid-cols-2">
                        <div className="space-y-2">
                            <Label htmlFor="first_name">
                                {t('mechanics.first_name')}{' '}
                                <span className="text-red-500">*</span>
                            </Label>
                            <Input
                                id="first_name"
                                value={data.first_name}
                                onChange={(e) => {
                                    setData('first_name', e.target.value);
                                    clearErrors('first_name');
                                }}
                                disabled={processing}
                                className={
                                    errors.first_name ? 'border-red-500' : ''
                                }
                            />
                            {errors.first_name && (
                                <p className="text-sm text-red-500">
                                    {errors.first_name}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="last_name">
                                {t('mechanics.last_name')}{' '}
                                <span className="text-red-500">*</span>
                            </Label>
                            <Input
                                id="last_name"
                                value={data.last_name}
                                onChange={(e) => {
                                    setData('last_name', e.target.value);
                                    clearErrors('last_name');
                                }}
                                disabled={processing}
                                className={
                                    errors.last_name ? 'border-red-500' : ''
                                }
                            />
                            {errors.last_name && (
                                <p className="text-sm text-red-500">
                                    {errors.last_name}
                                </p>
                            )}
                        </div>
                    </div>

                    <div className="space-y-2">
                        <div className="flex items-center justify-between">
                            <Label htmlFor="is_active">
                                {t('mechanics.is_active')}
                            </Label>
                            <Switch
                                id="is_active"
                                checked={data.is_active}
                                onCheckedChange={(checked) => {
                                    setData('is_active', checked);
                                }}
                                disabled={processing}
                            />
                        </div>
                        <p className="text-sm text-muted-foreground">
                            {t('mechanics.is_active_description')}
                        </p>
                    </div>

                    <div className="flex justify-end gap-4">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => window.history.back()}
                            disabled={processing}
                        >
                            {t('cancel')}
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? t('loading') : t('save')}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    );
}
