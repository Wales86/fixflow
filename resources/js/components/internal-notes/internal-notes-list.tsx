import { router } from '@inertiajs/react';
import { format } from 'date-fns';
import { pl } from 'date-fns/locale';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Check, FileText, Pencil, Trash2, User, X } from 'lucide-react';
import { useState } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Textarea } from '@/components/ui/textarea';

interface InternalNotesListProps {
    notes: App.Dto.InternalNote.InternalNoteData[];
    can_edit: boolean;
    can_delete: boolean;
}

export function InternalNotesList({
    notes,
    can_edit,
    can_delete,
}: InternalNotesListProps) {
    const { t } = useLaravelReactI18n();
    const [editingNoteId, setEditingNoteId] = useState<number | null>(null);
    const [editContent, setEditContent] = useState('');
    const [showDeleteDialog, setShowDeleteDialog] = useState(false);
    const [noteToDelete, setNoteToDelete] = useState<number | null>(null);
    const [isUpdating, setIsUpdating] = useState(false);

    const formatDate = (dateString: string): string => {
        return format(new Date(dateString), 'dd MMMM yyyy, HH:mm', {
            locale: pl,
        });
    };

    const handleStartEdit = (note: App.Dto.InternalNote.InternalNoteData) => {
        setEditingNoteId(note.id);
        setEditContent(note.content);
    };

    const handleSaveEdit = (noteId: number) => {
        setIsUpdating(true);
        router.patch(
            `/internal-notes/${noteId}`,
            {
                content: editContent,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setEditingNoteId(null);
                    setEditContent('');
                    setIsUpdating(false);
                },
                onError: () => {
                    setIsUpdating(false);
                },
            },
        );
    };

    const handleCancelEdit = () => {
        setEditingNoteId(null);
        setEditContent('');
    };

    const handleDeleteClick = (noteId: number) => {
        setNoteToDelete(noteId);
        setShowDeleteDialog(true);
    };

    const handleConfirmDelete = () => {
        if (noteToDelete) {
            router.delete(`/internal-notes/${noteToDelete}`, {
                preserveScroll: true,
                onSuccess: () => {
                    setShowDeleteDialog(false);
                    setNoteToDelete(null);
                },
            });
        }
    };

    if (notes.length === 0) {
        return (
            <Card>
                <CardContent className="flex flex-col items-center justify-center py-12">
                    <FileText className="mb-4 h-12 w-12 text-muted-foreground" />
                    <p className="text-muted-foreground">
                        {t('no_internal_notes')}
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
                        {notes.map((note) => {
                            const isEditing = editingNoteId === note.id;

                            return (
                                <div
                                    key={note.id}
                                    className="space-y-2 p-4 hover:bg-muted/50"
                                >
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-2">
                                            <User className="h-4 w-4 text-muted-foreground" />
                                            <span className="font-medium">
                                                {note.author
                                                    ? note.author.name
                                                    : t('unknown_author')}
                                            </span>
                                            <span className="text-xs text-muted-foreground">
                                                • {formatDate(note.created_at)}
                                            </span>
                                        </div>

                                        {!isEditing && (
                                            <div className="flex gap-1">
                                                {can_edit && (
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        onClick={() =>
                                                            handleStartEdit(
                                                                note,
                                                            )
                                                        }
                                                        className="h-8 w-8 p-0"
                                                    >
                                                        <Pencil className="h-4 w-4" />
                                                        <span className="sr-only">
                                                            {t('edit_note')}
                                                        </span>
                                                    </Button>
                                                )}
                                                {can_delete && (
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        onClick={() =>
                                                            handleDeleteClick(
                                                                note.id,
                                                            )
                                                        }
                                                        className="h-8 w-8 p-0 text-destructive hover:text-destructive"
                                                    >
                                                        <Trash2 className="h-4 w-4" />
                                                        <span className="sr-only">
                                                            {t('delete_note')}
                                                        </span>
                                                    </Button>
                                                )}
                                            </div>
                                        )}
                                    </div>

                                    {isEditing ? (
                                        <div className="space-y-2">
                                            <Textarea
                                                value={editContent}
                                                onChange={(e) =>
                                                    setEditContent(
                                                        e.target.value,
                                                    )
                                                }
                                                rows={4}
                                                disabled={isUpdating}
                                                className="text-sm"
                                                autoFocus
                                            />
                                            <div className="flex justify-end gap-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={handleCancelEdit}
                                                    disabled={isUpdating}
                                                >
                                                    <X className="mr-1 h-4 w-4" />
                                                    {t('cancel')}
                                                </Button>
                                                <Button
                                                    size="sm"
                                                    onClick={() =>
                                                        handleSaveEdit(note.id)
                                                    }
                                                    disabled={
                                                        isUpdating ||
                                                        !editContent.trim()
                                                    }
                                                >
                                                    <Check className="mr-1 h-4 w-4" />
                                                    {isUpdating
                                                        ? t('saving')
                                                        : t('save')}
                                                </Button>
                                            </div>
                                        </div>
                                    ) : (
                                        <p className="text-sm leading-relaxed">
                                            {note.content}
                                        </p>
                                    )}
                                </div>
                            );
                        })}
                    </div>
                </CardContent>
            </Card>

            <Dialog open={showDeleteDialog} onOpenChange={setShowDeleteDialog}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {t('delete_note_confirm_title')}
                        </DialogTitle>
                        <DialogDescription>
                            {t('delete_note_confirm_description')}
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setShowDeleteDialog(false)}
                        >
                            {t('cancel')}
                        </Button>
                        <Button
                            variant="destructive"
                            onClick={handleConfirmDelete}
                        >
                            {t('delete')}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}
