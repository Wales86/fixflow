import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { MechanicData } from '@/types/generated';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

interface Props {
    mechanic: MechanicData;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mechanicy',
        href: '/mechanics',
    },
    {
        title: 'Edycja mechanika',
        href: '#',
    },
];

export default function MechanicsEdit({ mechanic }: Props) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edytuj mechanika" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <h1 className="text-2xl font-bold tracking-tight">
                    Edytuj mechanika: {mechanic.first_name} {mechanic.last_name}
                </h1>

                <Card>
                    <CardHeader>
                        <CardTitle>Dane mechanika</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="text-muted-foreground">
                            Formularz edycji będzie tutaj...
                        </p>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

