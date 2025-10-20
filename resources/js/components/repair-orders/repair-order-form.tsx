import { useForm } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { FormEventHandler } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { toast } from 'sonner';
import { ImageUploader } from './image-uploader';
import { VehicleCombobox } from './vehicle-combobox';

interface RepairOrderFormProps {
    isEditMode?: boolean;
    initialData?: App.Dto.RepairOrder.RepairOrderFormData;
    vehicles: App.Dto.RepairOrder.VehicleSelectionData[];
    statuses: App.Dto.Common.SelectOptionData[];
    preselectedVehicleId?: number | null;
}

export function RepairOrderForm({
    isEditMode = false,
    initialData,
    vehicles,
    statuses,
    preselectedVehicleId,
}: RepairOrderFormProps) {
    const { t } = useLaravelReactI18n();

    // Wspólny model dla obu trybów - używamy najbardziej kompletnej struktury
    const { data, setData, post, patch, processing, errors, clearErrors } =
        useForm({
            vehicle_id: initialData?.vehicle_id ?? preselectedVehicleId ?? null,
            description: initialData?.problem_description ?? '',
            status: initialData?.status ?? null,
            attachments: null as File[] | null,
        });

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEditMode && initialData) {
            // W trybie edycji backend oczekuje tylko description i status
            patch(`/repair-orders/${initialData.id}`, {
                preserveScroll: true,
                onError: () => {
                    toast.error(t('an_error_occurred'));
                },
            });
        } else {
            // W trybie tworzenia backend oczekuje vehicle_id, description i attachments
            post('/repair-orders', {
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
                        ? t('edit_repair_order_data')
                        : t('repair_order_data')}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Vehicle Selection - Only in Create Mode */}
                    {!isEditMode && (
                        <div className="space-y-2">
                            <Label htmlFor="vehicle_id">
                                {t('vehicle')}{' '}
                                <span className="text-red-500">*</span>
                            </Label>
                            <VehicleCombobox
                                options={vehicles}
                                value={data.vehicle_id}
                                onChange={(value) => {
                                    setData('vehicle_id', value);
                                    clearErrors('vehicle_id');
                                }}
                                disabled={processing}
                            />
                            {errors.vehicle_id && (
                                <p className="text-sm text-red-500">
                                    {errors.vehicle_id}
                                </p>
                            )}
                        </div>
                    )}

                    {/* Problem Description */}
                    <div className="space-y-2">
                        <Label htmlFor="description">
                            {t('problem_description')}{' '}
                            <span className="text-red-500">*</span>
                        </Label>
                        <Textarea
                            id="description"
                            value={data.description}
                            onChange={(e) => {
                                setData('description', e.target.value);
                                clearErrors('description');
                            }}
                            disabled={processing}
                            className={
                                errors.description ? 'border-red-500' : ''
                            }
                            rows={5}
                            placeholder={t('describe_the_problem')}
                        />
                        {errors.description && (
                            <p className="text-sm text-red-500">
                                {errors.description}
                            </p>
                        )}
                    </div>

                    {/* Status - Only in Edit Mode */}
                    {isEditMode && (
                        <div className="space-y-2">
                            <Label htmlFor="status">
                                {t('status')}{' '}
                                <span className="text-red-500">*</span>
                            </Label>
                            <Select
                                value={data.status || undefined}
                                onValueChange={(value) => {
                                    setData(
                                        'status',
                                        value as App.Enums.RepairOrderStatus,
                                    );
                                    clearErrors('status');
                                }}
                                disabled={processing}
                            >
                                <SelectTrigger
                                    className={
                                        errors.status ? 'border-red-500' : ''
                                    }
                                >
                                    <SelectValue
                                        placeholder={t('select_status')}
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    {statuses.map((status) => (
                                        <SelectItem
                                            key={status.value}
                                            value={status.value}
                                        >
                                            {status.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.status && (
                                <p className="text-sm text-red-500">
                                    {errors.status}
                                </p>
                            )}
                        </div>
                    )}

                    {/* Image Uploader - Only in Create Mode */}
                    {!isEditMode && (
                        <div className="space-y-2">
                            <Label>{t('attachments')}</Label>
                            <ImageUploader
                                value={data.attachments}
                                onChange={(files) => {
                                    setData('attachments', files);
                                    clearErrors('attachments');
                                }}
                                disabled={processing}
                                maxFiles={10}
                                maxSizeInMB={5}
                            />
                            {errors.attachments && (
                                <p className="text-sm text-red-500">
                                    {errors.attachments}
                                </p>
                            )}
                        </div>
                    )}

                    {/* Existing Images Display - In Edit Mode */}
                    {isEditMode &&
                        initialData &&
                        initialData.images.length > 0 && (
                            <div className="space-y-2">
                                <Label>{t('existing_attachments')}</Label>
                                <div className="grid grid-cols-3 gap-4">
                                    {initialData.images.map((image) => (
                                        <div
                                            key={image.id}
                                            className="relative aspect-square overflow-hidden rounded-lg border"
                                        >
                                            <img
                                                src={image.url}
                                                alt={image.name}
                                                className="h-full w-full object-cover"
                                            />
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                    {/* Form Actions */}
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
