import { useLaravelReactI18n } from 'laravel-react-i18n';

import { ActivityLogList } from '@/components/activity-log/activity-log-list';
import { InternalNotesList } from '@/components/internal-notes/internal-notes-list';
import { TimeEntriesList } from '@/components/time-entries/time-entries-list';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

interface RepairOrderTabsProps {
    time_entries: App.Dto.TimeTracking.TimeEntryData[];
    internal_notes: App.Dto.InternalNote.InternalNoteData[];
    activity_log: App.Dto.Common.ActivityLogData[];
    repairOrderId: number;
}

export function RepairOrderTabs({
    time_entries,
    internal_notes,
    activity_log,
    repairOrderId,
}: RepairOrderTabsProps) {
    const { t } = useLaravelReactI18n();

    return (
        <Tabs defaultValue="time-entries" className="w-full">
            <TabsList>
                <TabsTrigger value="time-entries">
                    {t('time_entries')} ({time_entries.length})
                </TabsTrigger>
                <TabsTrigger value="notes">
                    {t('internal_notes')} ({internal_notes.length})
                </TabsTrigger>
                <TabsTrigger value="history">
                    {t('activity_log')} ({activity_log.length})
                </TabsTrigger>
            </TabsList>

            <TabsContent value="time-entries">
                <TimeEntriesList
                    entries={time_entries}
                    repairOrderId={repairOrderId}
                />
            </TabsContent>

            <TabsContent value="notes">
                <InternalNotesList notes={internal_notes} />
            </TabsContent>

            <TabsContent value="history">
                <ActivityLogList logs={activity_log} />
            </TabsContent>
        </Tabs>
    );
}
