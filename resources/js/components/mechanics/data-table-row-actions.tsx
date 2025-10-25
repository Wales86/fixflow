import { router } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { MoreHorizontal, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';

import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { usePermission } from '@/lib/permissions';
import { destroy, edit } from '@/routes/mechanics';
import { toast } from 'sonner';

interface DataTableRowActionsProps {
    mechanic: App.Dto.Mechanic.MechanicData;
}

export function DataTableRowActions({ mechanic }: DataTableRowActionsProps) {
    const { t } = useLaravelReactI18n();
    const [showDeleteDialog, setShowDeleteDialog] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);

    const canUpdate = usePermission('update_mechanics');
    const canDelete = usePermission('delete_mechanics');

    const handleEdit = () => {
        router.visit(edit(mechanic.id).url);
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(destroy(mechanic.id).url, {
            onSuccess: () => {
                setShowDeleteDialog(false);
                toast.success(t('mechanics.deleted_successfully'));
            },
            onError: (errors) => {
                const errorMessage =
                    errors.message ||
                    t('mechanics.cannot_delete_has_time_entries');
                toast.error(errorMessage);
            },
            onFinish: () => {
                setIsDeleting(false);
            },
        });
    };

    // Jeśli użytkownik nie ma żadnych uprawnień, nie wyświetlaj menu
    if (!canUpdate && !canDelete) {
        return null;
    }

    return (
        <>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="sm" className="size-8 p-0">
                        <span className="sr-only">
                            {t('mechanics.open_menu')}
                        </span>
                        <MoreHorizontal className="size-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuLabel>{t('actions')}</DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    {canUpdate && (
                        <DropdownMenuItem onClick={handleEdit}>
                            <Pencil className="mr-2 size-4" />
                            {t('edit')}
                        </DropdownMenuItem>
                    )}
                    {canDelete && (
                        <DropdownMenuItem
                            onClick={() => setShowDeleteDialog(true)}
                            className="text-destructive"
                        >
                            <Trash2 className="mr-2 size-4" />
                            {t('delete')}
                        </DropdownMenuItem>
                    )}
                </DropdownMenuContent>
            </DropdownMenu>

            <Dialog open={showDeleteDialog} onOpenChange={setShowDeleteDialog}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {t('mechanics.delete_confirmation_title')}
                        </DialogTitle>
                        <DialogDescription>
                            {t('mechanics.delete_confirmation_description', {
                                name: `${mechanic.first_name} ${mechanic.last_name}`,
                            })}
                            {mechanic.time_entries_count &&
                                mechanic.time_entries_count > 0 && (
                                    <span className="mt-2 block text-yellow-600 dark:text-yellow-500">
                                        {t(
                                            'mechanics.has_time_entries_warning',
                                            {
                                                count: mechanic.time_entries_count,
                                            },
                                        )}
                                    </span>
                                )}
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setShowDeleteDialog(false)}
                            disabled={isDeleting}
                        >
                            {t('cancel')}
                        </Button>
                        <Button
                            variant="destructive"
                            onClick={handleDelete}
                            disabled={isDeleting}
                        >
                            {isDeleting ? t('loading') : t('delete')}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}
