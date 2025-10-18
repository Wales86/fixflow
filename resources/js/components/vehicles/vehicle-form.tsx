import { FormEventHandler } from "react";
import { useForm } from "@inertiajs/react";

import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { ClientCombobox } from "./client-combobox";
import { toast } from "sonner";

interface VehicleFormProps {
    vehicle?: App.Dto.Vehicle.VehicleData;
    clients: App.Dto.Client.ClientSelectOptionData[];
    preselectedClientId?: number;
}

const currentYear = new Date().getFullYear();

export function VehicleForm({
    vehicle,
    clients,
    preselectedClientId,
}: VehicleFormProps) {
    const isEditMode = !!vehicle;

    const { data, setData, post, put, processing, errors, clearErrors } = useForm({
        client_id: vehicle?.client_id ?? preselectedClientId ?? null,
        make: vehicle?.make ?? "",
        model: vehicle?.model ?? "",
        year: vehicle?.year ?? "",
        vin: vehicle?.vin ?? "",
        registration_number: vehicle?.registration_number ?? "",
    });

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEditMode) {
            put(`/vehicles/${vehicle.id}`, {
                preserveScroll: true,
                onError: () => {
                    toast.error("Wystąpił błąd podczas aktualizacji pojazdu");
                },
            });
        } else {
            post("/vehicles", {
                preserveScroll: true,
                onError: () => {
                    toast.error("Wystąpił błąd podczas tworzenia pojazdu");
                },
            });
        }
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>
                    {isEditMode ? "Edytuj dane pojazdu" : "Dane pojazdu"}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="space-y-2">
                        <Label htmlFor="client_id">
                            Klient <span className="text-red-500">*</span>
                        </Label>
                        <ClientCombobox
                            options={clients}
                            value={data.client_id}
                            onChange={(value) => {
                                setData("client_id", value);
                                clearErrors("client_id");
                            }}
                            disabled={processing}
                        />
                        {errors.client_id && (
                            <p className="text-sm text-red-500">{errors.client_id}</p>
                        )}
                    </div>

                    <div className="grid gap-6 md:grid-cols-2">
                        <div className="space-y-2">
                            <Label htmlFor="make">
                                Marka <span className="text-red-500">*</span>
                            </Label>
                            <Input
                                id="make"
                                value={data.make}
                                onChange={(e) => {
                                    setData("make", e.target.value);
                                    clearErrors("make");
                                }}
                                disabled={processing}
                                className={errors.make ? "border-red-500" : ""}
                            />
                            {errors.make && (
                                <p className="text-sm text-red-500">{errors.make}</p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="model">
                                Model <span className="text-red-500">*</span>
                            </Label>
                            <Input
                                id="model"
                                value={data.model}
                                onChange={(e) => {
                                    setData("model", e.target.value);
                                    clearErrors("model");
                                }}
                                disabled={processing}
                                className={errors.model ? "border-red-500" : ""}
                            />
                            {errors.model && (
                                <p className="text-sm text-red-500">{errors.model}</p>
                            )}
                        </div>
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="year">
                            Rok produkcji <span className="text-red-500">*</span>
                        </Label>
                        <Input
                            id="year"
                            type="number"
                            min={1900}
                            max={currentYear + 1}
                            value={data.year}
                            onChange={(e) => {
                                setData("year", e.target.value);
                                clearErrors("year");
                            }}
                            disabled={processing}
                            className={errors.year ? "border-red-500" : ""}
                        />
                        {errors.year && (
                            <p className="text-sm text-red-500">{errors.year}</p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="vin">
                            VIN <span className="text-red-500">*</span>
                        </Label>
                        <Input
                            id="vin"
                            value={data.vin}
                            onChange={(e) => {
                                setData("vin", e.target.value.toUpperCase());
                                clearErrors("vin");
                            }}
                            disabled={processing}
                            maxLength={17}
                            className={errors.vin ? "border-red-500" : ""}
                        />
                        {errors.vin && (
                            <p className="text-sm text-red-500">{errors.vin}</p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="registration_number">
                            Numer rejestracyjny <span className="text-red-500">*</span>
                        </Label>
                        <Input
                            id="registration_number"
                            value={data.registration_number}
                            onChange={(e) => {
                                setData("registration_number", e.target.value.toUpperCase());
                                clearErrors("registration_number");
                            }}
                            disabled={processing}
                            className={errors.registration_number ? "border-red-500" : ""}
                        />
                        {errors.registration_number && (
                            <p className="text-sm text-red-500">
                                {errors.registration_number}
                            </p>
                        )}
                    </div>

                    <div className="flex justify-end gap-4">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => window.history.back()}
                            disabled={processing}
                        >
                            Anuluj
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? "Zapisywanie..." : "Zapisz"}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    );
}
