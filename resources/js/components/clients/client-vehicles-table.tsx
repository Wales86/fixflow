import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { router } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';

interface ClientVehiclesTableProps {
    vehicles: App.Dto.Vehicle.VehicleData[];
    clientId: number;
}

export function ClientVehiclesTable({ vehicles, clientId }: ClientVehiclesTableProps) {
    const { t } = useLaravelReactI18n();

    const handleRowClick = (vehicleId: number) => {
        router.visit(`/vehicles/${vehicleId}`);
    };

    if (vehicles.length === 0) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>{t('vehicles')}</CardTitle>
                    <CardDescription>
                        {t('no_vehicles_for_client')}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div className="flex flex-col items-center justify-center gap-4 py-8">
                        <p className="text-sm text-muted-foreground">
                            {t('client_has_no_vehicles')}
                        </p>
                        <Button
                            onClick={() => {
                                router.visit(`/vehicles/create?preselected_client_id=${clientId}`);
                            }}
                        >
                            {t('add_vehicle')}
                        </Button>
                    </div>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardHeader>
                <CardTitle>{t('vehicles')}</CardTitle>
                <CardDescription>
                    {t('client_vehicles_list')}
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>{t('make')}</TableHead>
                            <TableHead>{t('model')}</TableHead>
                            <TableHead>{t('year')}</TableHead>
                            <TableHead>{t('registration_number')}</TableHead>
                            <TableHead>{t('vin')}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {vehicles.map((vehicle) => (
                            <TableRow
                                key={vehicle.id}
                                onClick={() => handleRowClick(vehicle.id)}
                                className="cursor-pointer"
                            >
                                <TableCell className="font-medium">
                                    {vehicle.make}
                                </TableCell>
                                <TableCell>{vehicle.model}</TableCell>
                                <TableCell>{vehicle.year}</TableCell>
                                <TableCell>
                                    {vehicle.registration_number}
                                </TableCell>
                                <TableCell className="font-mono text-xs">
                                    {vehicle.vin}
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    );
}
