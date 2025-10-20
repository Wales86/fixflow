import { format } from 'date-fns';
import { pl } from 'date-fns/locale';
import { useLaravelReactI18n } from 'laravel-react-i18n';

import { Card, CardContent } from '@/components/ui/card';
import { Clock, User } from 'lucide-react';

interface TimeEntriesListProps {
    entries: App.Dto.TimeTracking.TimeEntryData[];
}

export function TimeEntriesList({ entries }: TimeEntriesListProps) {
    const { t } = useLaravelReactI18n();

    const formatDate = (dateString: string): string => {
        return format(new Date(dateString), 'dd MMMM yyyy, HH:mm', {
            locale: pl,
        });
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
                            <div className="ml-4 flex flex-col items-end gap-1">
                                <div className="flex items-center gap-1 text-sm font-semibold">
                                    <Clock className="h-4 w-4" />
                                    {entry.duration_hours}h{' '}
                                    {entry.duration_minutes % 60}m
                                </div>
                                <div className="text-xs text-muted-foreground">
                                    ({entry.duration_minutes} {t('minutes')})
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </CardContent>
        </Card>
    );
}
