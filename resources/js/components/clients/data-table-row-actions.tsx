import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { router } from '@inertiajs/react';
import { Eye, MoreHorizontal, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface DataTableRowActionsProps {
    client: App.Dto.Client.ClientListItemData;
}

export function DataTableRowActions({ client }: DataTableRowActionsProps) {
    const [showDeleteDialog, setShowDeleteDialog] = useState(false);

    const handleView = () => {
        router.visit(`/clients/${client.id}`);
    };

    const handleEdit = () => {
        router.visit(`/clients/${client.id}/edit`);
    };

    const handleDelete = () => {
        router.delete(`/clients/${client.id}`, {
            onSuccess: () => {
                setShowDeleteDialog(false);
            },
        });
    };

    return (
        <>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="sm" className="size-8 p-0">
                        <span className="sr-only">Otwórz menu</span>
                        <MoreHorizontal className="size-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuLabel>Akcje</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem onClick={handleView}>
                        <Eye className="mr-2 size-4" />
                        Zobacz
                    </DropdownMenuItem>
                    <DropdownMenuItem onClick={handleEdit}>
                        <Pencil className="mr-2 size-4" />
                        Edytuj
                    </DropdownMenuItem>
                    <DropdownMenuItem
                        onClick={() => setShowDeleteDialog(true)}
                        className="text-destructive"
                    >
                        <Trash2 className="mr-2 size-4" />
                        Usuń
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <Dialog open={showDeleteDialog} onOpenChange={setShowDeleteDialog}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Czy na pewno chcesz usunąć tego klienta?</DialogTitle>
                        <DialogDescription>
                            Ta akcja jest nieodwracalna. Klient{' '}
                            <strong>
                                {client.first_name} {client.last_name}
                            </strong>{' '}
                            zostanie trwale usunięty z systemu.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setShowDeleteDialog(false)}
                        >
                            Anuluj
                        </Button>
                        <Button variant="destructive" onClick={handleDelete}>
                            Usuń
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}
