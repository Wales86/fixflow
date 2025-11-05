import { StatusBadge } from '@/components/status-badge';
import { TimeEntryDialog } from '@/components/time-entries/time-entry-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { usePermission } from '@/lib/permissions';
import { Link } from '@inertiajs/react';
import { Clock, FileText, Plus } from 'lucide-react';
import { useState } from 'react';
import { AddNoteDialog } from '../internal-notes/add-note-dialog';

interface RepairOrderCardProps {
    order: App.Dto.RepairOrder.MechanicRepairOrderCardData;
}

export function RepairOrderCard({ order }: RepairOrderCardProps) {
    const canCreateTimeEntry = usePermission('create_time_entries');
    const canCreateNote = usePermission('create_internal_notes');
    const [isTimeEntryDialogOpen, setTimeEntryDialogOpen] = useState(false);
    const [isNoteDialogOpen, setNoteDialogOpen] = useState(false);

    const totalHours = Math.floor(order.total_time_minutes / 60);
    const totalMinutes = order.total_time_minutes % 60;

    const handleAddTimeEntry = (e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setTimeEntryDialogOpen(true);
    };

    const handleAddNote = (e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setNoteDialogOpen(true);
    };

    return (
        <>
            <Link href={`/repair-orders/${order.id}`}>
                <Card className="flex h-full flex-col justify-between transition-all hover:shadow-md">
                    <CardContent className="p-4">
                        <div className="flex flex-col gap-3">
                            {/* Header with status and add button */}
                            <div className="flex items-start justify-between gap-2">
                                <div className="flex-1">
                                    <p className="text-sm text-muted-foreground">
                                        #{order.id}
                                    </p>
                                </div>
                                <div className="flex items-center gap-2">
                                    <StatusBadge status={order.status} />
                                </div>
                            </div>

                            {/* Vehicle info */}
                            <div>
                                <h3 className="font-semibold">
                                    {order.vehicle.make} {order.vehicle.model}
                                </h3>
                                <p className="text-sm text-muted-foreground">
                                    {order.vehicle.registration_number}
                                </p>
                            </div>

                            {/* Client info */}
                            <div>
                                <p className="text-sm">
                                    {order.client.first_name}{' '}
                                    {order.client.last_name}
                                </p>
                            </div>

                            {/* Problem description */}
                            <div>
                                <p className="line-clamp-2 text-sm text-muted-foreground">
                                    {order.problem_description}
                                </p>
                            </div>

                            {/* Time tracking */}
                            <div className="flex items-center gap-1.5 text-sm text-muted-foreground">
                                <Clock className="size-4" />
                                <span>
                                    {totalHours > 0 && `${totalHours}h `}
                                    {totalMinutes}min
                                </span>
                            </div>
                        </div>
                    </CardContent>
                    {(canCreateTimeEntry || canCreateNote) && (
                        <CardFooter className="flex flex-col gap-2 p-4 pt-0">
                            {canCreateTimeEntry && (
                                <Button
                                    size="sm"
                                    className="w-full cursor-pointer"
                                    onClick={handleAddTimeEntry}
                                >
                                    <Clock className="mr-2 size-4" />
                                    Dodaj czas pracy
                                </Button>
                            )}
                            {canCreateNote && (
                                <Button
                                    variant="outline"
                                    size="sm"
                                    className="w-full cursor-pointer"
                                    onClick={handleAddNote}
                                >
                                    <FileText className="mr-2 size-4" />
                                    Dodaj notatkę
                                </Button>
                            )}
                        </CardFooter>
                    )}
                </Card>
            </Link>

            <TimeEntryDialog
                isOpen={isTimeEntryDialogOpen}
                onClose={() => setTimeEntryDialogOpen(false)}
                repairOrderId={order.id}
            />
            <AddNoteDialog
                notableType="repair_order"
                notableId={order.id}
                isOpen={isNoteDialogOpen}
                onClose={() => setNoteDialogOpen(false)}
            />
        </>
    );
}
