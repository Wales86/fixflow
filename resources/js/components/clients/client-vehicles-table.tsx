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

interface ClientVehiclesTableProps {
    vehicles: App.Dto.Vehicle.VehicleData[];
}

export function ClientVehiclesTable({ vehicles }: ClientVehiclesTableProps) {
    const handleRowClick = (vehicleId: number) => {
        router.visit(`/vehicles/${vehicleId}`);
    };

    if (vehicles.length === 0) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>Pojazdy</CardTitle>
                    <CardDescription>
                        Brak pojazdów dla tego klienta
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div className="flex flex-col items-center justify-center gap-4 py-8">
                        <p className="text-muted-foreground text-sm">
                            Ten klient nie ma jeszcze przypisanych żadnych
                            pojazdów.
                        </p>
                        <Button
                            onClick={() => {
                                // TODO: Implementacja dodawania pojazdu
                                console.log('Dodaj pojazd');
                            }}
                        >
                            Dodaj pojazd
                        </Button>
                    </div>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardHeader>
                <CardTitle>Pojazdy</CardTitle>
                <CardDescription>
                    Lista pojazdów przypisanych do klienta
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Marka</TableHead>
                            <TableHead>Model</TableHead>
                            <TableHead>Rok</TableHead>
                            <TableHead>Nr. rejestracyjny</TableHead>
                            <TableHead>VIN</TableHead>
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
