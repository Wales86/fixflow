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
import { destroy, edit } from '@/routes/users';

interface DataTableRowActionsProps {
    user: App.Dto.User.UserData;
}

export function DataTableRowActions({ user }: DataTableRowActionsProps) {
    const { t } = useLaravelReactI18n();
    const [showDeleteDialog, setShowDeleteDialog] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);

    const canUpdate = usePermission('update_users');
    const canDelete = usePermission('delete_users');

    const handleEdit = () => {
        router.visit(edit(user.id).url);
    };

    const handleDelete = () => {
        setIsDeleting(true);
        router.delete(destroy(user.id).url, {
            onSuccess: () => {
                setShowDeleteDialog(false);
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
                        <span className="sr-only">{t('users.open_menu')}</span>
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
                            {t('users.delete_confirmation_title')}
                        </DialogTitle>
                        <DialogDescription>
                            {t('users.delete_confirmation_description', {
                                name: user.name,
                            })}
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
