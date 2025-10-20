import { format } from 'date-fns';
import { pl } from 'date-fns/locale';
import { useLaravelReactI18n } from 'laravel-react-i18n';

import { Card, CardContent } from '@/components/ui/card';
import { FileText, User } from 'lucide-react';

interface InternalNotesListProps {
    notes: App.Dto.InternalNote.InternalNoteData[];
}

export function InternalNotesList({ notes }: InternalNotesListProps) {
    const { t } = useLaravelReactI18n();

    const formatDate = (dateString: string): string => {
        return format(new Date(dateString), 'dd MMMM yyyy, HH:mm', {
            locale: pl,
        });
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
        <Card>
            <CardContent className="p-0">
                <div className="divide-y">
                    {notes.map((note) => (
                        <div
                            key={note.id}
                            className="space-y-2 p-4 hover:bg-muted/50"
                        >
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
                            <p className="text-sm leading-relaxed">
                                {note.content}
                            </p>
                        </div>
                    ))}
                </div>
            </CardContent>
        </Card>
    );
}
