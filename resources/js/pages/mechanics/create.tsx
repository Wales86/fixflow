import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mechanicy',
        href: '/mechanics',
    },
    {
        title: 'Nowy mechanik',
        href: '/mechanics/create',
    },
];

export default function MechanicsCreate() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dodaj mechanika" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    Dodaj nowego mechanika
                </h1>

                <Card>
                    <CardHeader>
                        <CardTitle>Dane mechanika</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="text-muted-foreground">
                            Formularz będzie tutaj...
                        </p>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
