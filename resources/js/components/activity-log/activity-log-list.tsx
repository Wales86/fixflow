import { format } from 'date-fns';
import { pl } from 'date-fns/locale';
import { useLaravelReactI18n } from 'laravel-react-i18n';

import { Card, CardContent } from '@/components/ui/card';
import { History, User } from 'lucide-react';

interface ActivityLogListProps {
    logs: App.Dto.Common.ActivityLogData[];
}

export function ActivityLogList({ logs }: ActivityLogListProps) {
    const { t } = useLaravelReactI18n();

    const formatDate = (dateString: string): string => {
        return format(new Date(dateString), 'dd MMMM yyyy, HH:mm', {
            locale: pl,
        });
    };

    if (logs.length === 0) {
        return (
            <Card>
                <CardContent className="flex flex-col items-center justify-center py-12">
                    <History className="mb-4 h-12 w-12 text-muted-foreground" />
                    <p className="text-muted-foreground">
                        {t('no_activity_log')}
                    </p>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardContent className="p-0">
                <div className="divide-y">
                    {logs.map((log) => (
                        <div
                            key={log.id}
                            className="flex items-start gap-3 p-4 hover:bg-muted/50"
                        >
                            <div className="mt-1 rounded-full bg-muted p-2">
                                <History className="h-4 w-4 text-muted-foreground" />
                            </div>
                            <div className="flex-1 space-y-1">
                                <div className="flex items-center gap-2 text-xs text-muted-foreground">
                                    <span>{formatDate(log.created_at)}</span>
                                    {log.causer && (
                                        <>
                                            <span>•</span>
                                            <User className="h-3 w-3" />
                                            <span className="font-medium text-foreground">
                                                {log.causer.name}
                                            </span>
                                        </>
                                    )}
                                </div>
                                <p className="text-sm leading-relaxed">
                                    {log.description}
                                </p>
                                {log.event && (
                                    <div className="inline-block rounded-md bg-muted px-2 py-1 text-xs font-medium">
                                        {log.event}
                                    </div>
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            </CardContent>
        </Card>
    );
}
