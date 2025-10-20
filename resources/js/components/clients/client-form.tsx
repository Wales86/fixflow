import { useForm } from '@inertiajs/react';
import { type FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store, update } from '@/routes/clients';

interface ClientFormProps {
    client?: App.Dto.Client.ClientData;
}

type ClientFormViewModel = {
    first_name: string;
    last_name: string;
    phone_number: string;
    email: string;
    address_street: string;
    address_city: string;
    address_postal_code: string;
    address_country: string;
};

export default function ClientForm({ client }: ClientFormProps) {
    const isEditMode = !!client;

    const { data, setData, post, put, processing, errors } =
        useForm<ClientFormViewModel>({
            first_name: client?.first_name ?? '',
            last_name: client?.last_name ?? '',
            phone_number: client?.phone_number ?? '',
            email: client?.email ?? '',
            address_street: client?.address_street ?? '',
            address_city: client?.address_city ?? '',
            address_postal_code: client?.address_postal_code ?? '',
            address_country: client?.address_country ?? '',
        });

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEditMode && client) {
            put(update(client.id).url);
        } else {
            post(store().url);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            {/* Dane osobowe */}
            <div className="space-y-4">
                <h3 className="text-lg font-medium">Dane osobowe</h3>

                <div className="grid gap-4 sm:grid-cols-2">
                    <div className="grid gap-2">
                        <Label htmlFor="first_name">
                            Imię <span className="text-destructive">*</span>
                        </Label>
                        <Input
                            id="first_name"
                            name="first_name"
                            value={data.first_name}
                            onChange={(e) =>
                                setData('first_name', e.target.value)
                            }
                            required
                            autoComplete="given-name"
                            placeholder="Jan"
                            aria-invalid={!!errors.first_name}
                        />
                        <InputError message={errors.first_name} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="last_name">
                            Nazwisko <span className="text-destructive">*</span>
                        </Label>
                        <Input
                            id="last_name"
                            name="last_name"
                            value={data.last_name}
                            onChange={(e) =>
                                setData('last_name', e.target.value)
                            }
                            required
                            autoComplete="family-name"
                            placeholder="Kowalski"
                            aria-invalid={!!errors.last_name}
                        />
                        <InputError message={errors.last_name} />
                    </div>
                </div>

                <div className="grid gap-4 sm:grid-cols-2">
                    <div className="grid gap-2">
                        <Label htmlFor="phone_number">
                            Numer telefonu{' '}
                            <span className="text-destructive">*</span>
                        </Label>
                        <Input
                            id="phone_number"
                            name="phone_number"
                            type="tel"
                            value={data.phone_number}
                            onChange={(e) =>
                                setData('phone_number', e.target.value)
                            }
                            required
                            autoComplete="tel"
                            placeholder="+48 123 456 789"
                            aria-invalid={!!errors.phone_number}
                        />
                        <InputError message={errors.phone_number} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="email">Email</Label>
                        <Input
                            id="email"
                            name="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            autoComplete="email"
                            placeholder="jan.kowalski@example.com"
                            aria-invalid={!!errors.email}
                        />
                        <InputError message={errors.email} />
                    </div>
                </div>
            </div>

            {/* Dane adresowe */}
            <div className="space-y-4">
                <h3 className="text-lg font-medium">Dane adresowe</h3>

                <div className="grid gap-2">
                    <Label htmlFor="address_street">Ulica</Label>
                    <Input
                        id="address_street"
                        name="address_street"
                        value={data.address_street}
                        onChange={(e) =>
                            setData('address_street', e.target.value)
                        }
                        autoComplete="street-address"
                        placeholder="ul. Kwiatowa 15/3"
                        aria-invalid={!!errors.address_street}
                    />
                    <InputError message={errors.address_street} />
                </div>

                <div className="grid gap-4 sm:grid-cols-2">
                    <div className="grid gap-2">
                        <Label htmlFor="address_city">Miasto</Label>
                        <Input
                            id="address_city"
                            name="address_city"
                            value={data.address_city}
                            onChange={(e) =>
                                setData('address_city', e.target.value)
                            }
                            autoComplete="address-level2"
                            placeholder="Warszawa"
                            aria-invalid={!!errors.address_city}
                        />
                        <InputError message={errors.address_city} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="address_postal_code">
                            Kod pocztowy
                        </Label>
                        <Input
                            id="address_postal_code"
                            name="address_postal_code"
                            value={data.address_postal_code}
                            onChange={(e) =>
                                setData('address_postal_code', e.target.value)
                            }
                            autoComplete="postal-code"
                            placeholder="00-001"
                            aria-invalid={!!errors.address_postal_code}
                        />
                        <InputError message={errors.address_postal_code} />
                    </div>
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="address_country">Kraj</Label>
                    <Input
                        id="address_country"
                        name="address_country"
                        value={data.address_country}
                        onChange={(e) =>
                            setData('address_country', e.target.value)
                        }
                        autoComplete="country-name"
                        placeholder="Polska"
                        aria-invalid={!!errors.address_country}
                    />
                    <InputError message={errors.address_country} />
                </div>
            </div>

            {/* Akcje */}
            <div className="flex items-center gap-4">
                <Button type="submit" disabled={processing}>
                    {processing
                        ? isEditMode
                            ? 'Aktualizowanie...'
                            : 'Zapisywanie...'
                        : isEditMode
                          ? 'Zaktualizuj'
                          : 'Zapisz'}
                </Button>
            </div>
        </form>
    );
}
