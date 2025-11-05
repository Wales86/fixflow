import { Head } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

import { MechanicForm } from '@/components/mechanics/mechanic-form';
import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/mechanics';
import { type BreadcrumbItem } from '@/types';

export default function MechanicsCreate() {
    const { t } = useLaravelReactI18n();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('mechanics.title'),
            href: index().url,
        },
        {
            title: t('mechanics.new_mechanic'),
            href: '',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('mechanics.add_new_mechanic')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    {t('mechanics.add_new_mechanic')}
                </h1>
                <MechanicForm />
            </div>
        </AppLayout>
    );
}
