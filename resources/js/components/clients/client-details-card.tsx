import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface ClientDetailsCardProps {
    client: App.Dto.Client.ClientData;
}

export function ClientDetailsCard({ client }: ClientDetailsCardProps) {
    const formatValue = (value: string | null): string => {
        return value || '—';
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>Dane kontaktowe</CardTitle>
            </CardHeader>
            <CardContent>
                <dl className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt className="text-sm font-medium text-muted-foreground">
                            Imię
                        </dt>
                        <dd className="mt-1 text-sm">{client.first_name}</dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-muted-foreground">
                            Nazwisko
                        </dt>
                        <dd className="mt-1 text-sm">
                            {formatValue(client.last_name)}
                        </dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-muted-foreground">
                            Numer telefonu
                        </dt>
                        <dd className="mt-1 text-sm">{client.phone_number}</dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-muted-foreground">
                            Email
                        </dt>
                        <dd className="mt-1 text-sm">
                            {formatValue(client.email)}
                        </dd>
                    </div>

                    <div className="sm:col-span-2">
                        <dt className="text-sm font-medium text-muted-foreground">
                            Adres
                        </dt>
                        <dd className="mt-1 text-sm">
                            {client.address_street ? (
                                <>
                                    {client.address_street}
                                    {client.address_city && (
                                        <>
                                            <br />
                                            {client.address_postal_code}{' '}
                                            {client.address_city}
                                        </>
                                    )}
                                    {client.address_country && (
                                        <>
                                            <br />
                                            {client.address_country}
                                        </>
                                    )}
                                </>
                            ) : (
                                '—'
                            )}
                        </dd>
                    </div>
                </dl>
            </CardContent>
        </Card>
    );
}
