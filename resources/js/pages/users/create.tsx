import { Head } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/users';
import { type BreadcrumbItem } from '@/types';

interface UsersCreateProps {
    roles: Array<{ value: string; label: string }>;
}

export default function UsersCreate({ roles }: UsersCreateProps) {
    const { t } = useLaravelReactI18n();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('users.title'),
            href: index().url,
        },
        {
            title: t('users.new_user'),
            href: '',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('users.add_user')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    {t('users.add_user')}
                </h1>
                <div className="rounded-lg border p-4">
                    <p className="text-muted-foreground">
                        User form placeholder - roles: {JSON.stringify(roles)}
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
