import { StatusBadge } from '@/components/status-badge';
import { Card, CardContent } from '@/components/ui/card';
import { Link } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Clock } from 'lucide-react';

interface RepairOrderCardProps {
    order: App.Dto.RepairOrder.MechanicRepairOrderCardData;
}

export function RepairOrderCard({ order }: RepairOrderCardProps) {
    const { t } = useLaravelReactI18n();

    const totalHours = Math.floor(order.total_time_minutes / 60);
    const totalMinutes = order.total_time_minutes % 60;

    return (
        <Link href={`/repair-orders/${order.id}`}>
            <Card className="transition-all hover:shadow-md">
                <CardContent className="p-4">
                    <div className="flex flex-col gap-3">
                        {/* Header with status */}
                        <div className="flex items-start justify-between gap-2">
                            <div className="flex-1">
                                <p className="text-sm text-muted-foreground">
                                    #{order.id}
                                </p>
                            </div>
                            <StatusBadge status={order.status} />
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
            </Card>
        </Link>
    );
}
