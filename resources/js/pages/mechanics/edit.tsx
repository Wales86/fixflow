import { Head } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

import { MechanicForm } from '@/components/mechanics/mechanic-form';
import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/mechanics';
import { type BreadcrumbItem } from '@/types';

interface MechanicsEditProps {
    mechanic: App.Dto.Mechanic.MechanicData;
}

export default function MechanicsEdit({ mechanic }: MechanicsEditProps) {
    const { t } = useLaravelReactI18n();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('mechanics.title'),
            href: index().url,
        },
        {
            title: `${mechanic.first_name} ${mechanic.last_name}`,
            href: '',
        },
        {
            title: t('edit'),
            href: '',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head
                title={t('mechanics.edit_mechanic_title', {
                    name: `${mechanic.first_name} ${mechanic.last_name}`,
                })}
            />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    {t('mechanics.edit_mechanic_heading', {
                        name: `${mechanic.first_name} ${mechanic.last_name}`,
                    })}
                </h1>
                <MechanicForm mechanic={mechanic} />
            </div>
        </AppLayout>
    );
}
