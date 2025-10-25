import { router } from '@inertiajs/react';
import { format } from 'date-fns';
import { pl } from 'date-fns/locale';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Clock, Pencil, Trash2, User } from 'lucide-react';
import { useState } from 'react';

import { TimeEntryDialog } from '@/components/time-entries/time-entry-dialog';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { usePermission } from '@/lib/permissions';

interface TimeEntriesListProps {
    entries: App.Dto.TimeTracking.TimeEntryData[];
    repairOrderId: number;
}

export function TimeEntriesList({
    entries,
    repairOrderId,
}: TimeEntriesListProps) {
    const { t } = useLaravelReactI18n();
    const canUpdate = usePermission('update_time_entries');
    const canDelete = usePermission('delete_time_entries');

    const [editingEntry, setEditingEntry] =
        useState<App.Dto.TimeTracking.TimeEntryData | null>(null);
    const [deletingEntryId, setDeletingEntryId] = useState<number | null>(null);

    const formatDate = (dateString: string): string => {
        return format(new Date(dateString), 'dd MMMM yyyy, HH:mm', {
            locale: pl,
        });
    };

    const handleDelete = () => {
        if (deletingEntryId) {
            router.delete(`/time-entries/${deletingEntryId}`, {
                preserveScroll: true,
                onSuccess: () => setDeletingEntryId(null),
            });
        }
    };

    if (entries.length === 0) {
        return (
            <Card>
                <CardContent className="flex flex-col items-center justify-center py-12">
                    <Clock className="mb-4 h-12 w-12 text-muted-foreground" />
                    <p className="text-muted-foreground">
                        {t('no_time_entries')}
                    </p>
                </CardContent>
            </Card>
        );
    }

    return (
        <>
            <Card>
                <CardContent className="p-0">
                    <div className="divide-y">
                        {entries.map((entry) => (
                            <div
                                key={entry.id}
                                className="flex items-start justify-between p-4 hover:bg-muted/50"
                            >
                                <div className="flex-1 space-y-2">
                                    <div className="flex items-center gap-2">
                                        <User className="h-4 w-4 text-muted-foreground" />
                                        <span className="font-medium">
                                            {entry.mechanic
                                                ? `${entry.mechanic.first_name} ${entry.mechanic.last_name}`
                                                : t('unknown_mechanic')}
                                        </span>
                                    </div>
                                    {entry.description && (
                                        <p className="text-sm text-muted-foreground">
                                            {entry.description}
                                        </p>
                                    )}
                                    <div className="text-xs text-muted-foreground">
                                        {formatDate(entry.created_at)}
                                    </div>
                                </div>
                                <div className="ml-4 flex items-center gap-2">
                                    <div className="flex flex-col items-end gap-1">
                                        <div className="flex items-center gap-1 text-sm font-semibold">
                                            <Clock className="h-4 w-4" />
                                            {Math.floor(
                                                entry.duration_minutes / 60,
                                            )}{' '}
                                            h {entry.duration_minutes % 60}m
                                        </div>
                                        <div className="text-xs text-muted-foreground">
                                            ({entry.duration_minutes}{' '}
                                            {t('minutes')})
                                        </div>
                                    </div>

                                    {canUpdate && (
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={() =>
                                                setEditingEntry(entry)
                                            }
                                        >
                                            <Pencil className="h-4 w-4" />
                                        </Button>
                                    )}

                                    {canDelete && (
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={() =>
                                                setDeletingEntryId(entry.id)
                                            }
                                        >
                                            <Trash2 className="h-4 w-4 text-destructive" />
                                        </Button>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </CardContent>
            </Card>

            {/* Edit Dialog */}
            {editingEntry && (
                <TimeEntryDialog
                    isOpen={!!editingEntry}
                    onClose={() => setEditingEntry(null)}
                    repairOrderId={repairOrderId}
                    timeEntry={editingEntry}
                />
            )}

            {/* Delete Confirmation Dialog */}
            <AlertDialog
                open={!!deletingEntryId}
                onOpenChange={(open) => !open && setDeletingEntryId(null)}
            >
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>
                            {t('delete_time_entry')}
                        </AlertDialogTitle>
                        <AlertDialogDescription>
                            {t('delete_time_entry_confirm')}
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel
                            onClick={() => setDeletingEntryId(null)}
                        >
                            {t('cancel')}
                        </AlertDialogCancel>
                        <AlertDialogAction onClick={handleDelete}>
                            {t('delete_time_entry_button')}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}
