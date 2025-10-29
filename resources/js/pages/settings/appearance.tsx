import { Head } from '@inertiajs/react';

import AppearanceTabs from '@/components/appearance-tabs';
import HeadingSmall from '@/components/heading-small';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { edit as editAppearance } from '@/routes/appearance';
import { useLaravelReactI18n } from 'laravel-react-i18n';


export default function Appearance() {
    const { t } = useLaravelReactI18n();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('appearance_settings'),
            href: editAppearance().url,
        },
    ];
    
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('appearance_settings')} />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title={t('appearance_settings')}
                        description={t('update_your_account_appearance_settings')}
                    />
                    <AppearanceTabs />
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
