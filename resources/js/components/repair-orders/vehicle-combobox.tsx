import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Check, ChevronsUpDown } from 'lucide-react';
import { useState } from 'react';

import { Button } from '@/components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';

interface VehicleComboboxProps {
    options: App.Dto.RepairOrder.VehicleSelectionData[];
    value: number | null;
    onChange: (value: number) => void;
    disabled?: boolean;
}

export function VehicleCombobox({
    options,
    value,
    onChange,
    disabled = false,
}: VehicleComboboxProps) {
    const { t } = useLaravelReactI18n();
    const [open, setOpen] = useState(false);

    const selectedVehicle = options.find((vehicle) => vehicle.id === value);

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    role="combobox"
                    aria-expanded={open}
                    className="w-full justify-between"
                    disabled={disabled}
                >
                    {selectedVehicle ? (
                        <span className="flex items-center gap-2">
                            <span className="font-medium">
                                {selectedVehicle.display_name}
                            </span>
                            <span className="text-muted-foreground">
                                {selectedVehicle.registration_number}
                            </span>
                            <span className="text-sm text-muted-foreground">
                                ({selectedVehicle.client_name})
                            </span>
                        </span>
                    ) : (
                        t('select_vehicle')
                    )}
                    <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent
                className="w-[var(--radix-popover-trigger-width)] p-0"
                align="start"
            >
                <Command>
                    <CommandInput placeholder={t('search_vehicle')} />
                    <CommandList>
                        <CommandEmpty>{t('no_vehicle_found')}</CommandEmpty>
                        <CommandGroup>
                            {options.map((vehicle) => (
                                <CommandItem
                                    key={vehicle.id}
                                    value={`${vehicle.display_name} ${vehicle.registration_number} ${vehicle.client_name}`}
                                    onSelect={() => {
                                        onChange(vehicle.id);
                                        setOpen(false);
                                    }}
                                >
                                    <Check
                                        className={cn(
                                            'mr-2 h-4 w-4',
                                            value === vehicle.id
                                                ? 'opacity-100'
                                                : 'opacity-0',
                                        )}
                                    />
                                    <div className="flex flex-col">
                                        <div className="flex items-center gap-2">
                                            <span className="font-medium">
                                                {vehicle.display_name}
                                            </span>
                                            <span className="text-sm text-muted-foreground">
                                                {vehicle.registration_number}
                                            </span>
                                        </div>
                                        <span className="text-xs text-muted-foreground">
                                            {vehicle.client_name}
                                        </span>
                                    </div>
                                </CommandItem>
                            ))}
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
