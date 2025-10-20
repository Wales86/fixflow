import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Link } from '@inertiajs/react';

interface VehicleDetailsCardProps {
    vehicle: App.Dto.Vehicle.VehicleData;
}

export function VehicleDetailsCard({ vehicle }: VehicleDetailsCardProps) {
    const formatValue = (value: string | number | null): string => {
        if (value === null || value === '') {
            return '—';
        }
        return String(value);
    };

    const clientFullName = vehicle.client
        ? `${vehicle.client.first_name} ${vehicle.client.last_name || ''}`.trim()
        : null;

    return (
        <Card>
            <CardHeader>
                <CardTitle>Dane techniczne</CardTitle>
            </CardHeader>
            <CardContent>
                <dl className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt className="text-sm font-medium text-muted-foreground">
                            Marka
                        </dt>
                        <dd className="mt-1 text-sm">
                            {formatValue(vehicle.make)}
                        </dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-muted-foreground">
                            Model
                        </dt>
                        <dd className="mt-1 text-sm">
                            {formatValue(vehicle.model)}
                        </dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-muted-foreground">
                            Rok produkcji
                        </dt>
                        <dd className="mt-1 text-sm">{vehicle.year}</dd>
                    </div>

                    <div>
                        <dt className="text-sm font-medium text-muted-foreground">
                            Numer rejestracyjny
                        </dt>
                        <dd className="mt-1 text-sm">
                            {formatValue(vehicle.registration_number)}
                        </dd>
                    </div>

                    <div className="sm:col-span-2">
                        <dt className="text-sm font-medium text-muted-foreground">
                            VIN
                        </dt>
                        <dd className="mt-1 font-mono text-xs">
                            {formatValue(vehicle.vin)}
                        </dd>
                    </div>

                    <div className="sm:col-span-2">
                        <dt className="text-sm font-medium text-muted-foreground">
                            Właściciel
                        </dt>
                        <dd className="mt-1 text-sm">
                            {vehicle.client && clientFullName ? (
                                <Link
                                    href={`/clients/${vehicle.client.id}`}
                                    className="text-primary hover:underline"
                                >
                                    {clientFullName}
                                </Link>
                            ) : (
                                <span className="text-muted-foreground">
                                    Brak przypisanego klienta
                                </span>
                            )}
                        </dd>
                    </div>
                </dl>
            </CardContent>
        </Card>
    );
}
