import { Link } from '@inertiajs/react';
import { format } from 'date-fns';
import { pl } from 'date-fns/locale';
import { useLaravelReactI18n } from 'laravel-react-i18n';

import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Calendar,
    Car,
    Clock,
    ImageIcon,
    User,
    CalendarCheck,
    CalendarClock,
} from 'lucide-react';

interface RepairOrderDetailsCardProps {
    order: App.Dto.RepairOrder.RepairOrderShowData;
}

export function RepairOrderDetailsCard({ order }: RepairOrderDetailsCardProps) {
    const { t } = useLaravelReactI18n();

    const formatTime = (minutes: number): string => {
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        if (hours > 0 && mins > 0) {
            return `${hours}h ${mins}m`;
        } else if (hours > 0) {
            return `${hours}h`;
        } else {
            return `${mins}m`;
        }
    };

    const formatDate = (dateString: string): string => {
        return format(new Date(dateString), 'dd MMMM yyyy, HH:mm', {
            locale: pl,
        });
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>{t('order_details')}</CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
                {/* Client and Vehicle Info */}
                <div className="grid gap-6 md:grid-cols-2">
                    <div className="space-y-2">
                        <div className="flex items-center text-sm text-muted-foreground">
                            <User className="mr-2 h-4 w-4" />
                            {t('client')}
                        </div>
                        <Link
                            href={`/clients/${order.client.id}`}
                            className="text-base font-medium text-blue-600 hover:underline dark:text-blue-400"
                        >
                            {order.client.first_name}{' '}
                            {order.client.last_name || ''}
                        </Link>
                        <div className="text-sm text-muted-foreground">
                            {order.client.phone_number}
                        </div>
                    </div>

                    <div className="space-y-2">
                        <div className="flex items-center text-sm text-muted-foreground">
                            <Car className="mr-2 h-4 w-4" />
                            {t('vehicle')}
                        </div>
                        <Link
                            href={`/vehicles/${order.vehicle.id}`}
                            className="text-base font-medium text-blue-600 hover:underline dark:text-blue-400"
                        >
                            {order.vehicle.make} {order.vehicle.model}
                        </Link>
                        <div className="text-sm text-muted-foreground">
                            {order.vehicle.registration_number}
                        </div>
                    </div>
                </div>

                {/* Problem Description */}
                <div className="space-y-2">
                    <div className="text-sm font-medium">
                        {t('problem_description')}
                    </div>
                    <p className="text-sm text-muted-foreground">
                        {order.problem_description}
                    </p>
                </div>

                {/* Dates and Time */}
                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div className="space-y-2">
                        <div className="flex items-center text-sm text-muted-foreground">
                            <Calendar className="mr-2 h-4 w-4" />
                            {t('created_at')}
                        </div>
                        <div className="text-sm">
                            {formatDate(order.created_at)}
                        </div>
                    </div>
                    {order.started_at && (
                        <div className="space-y-2">
                            <div className="flex items-center text-sm text-muted-foreground">
                                <Calendar className="mr-2 h-4 w-4" />
                                {t('started_at')}
                            </div>
                            <div className="text-sm">
                                {formatDate(order.started_at)}
                            </div>
                        </div>
                    )}
                    <div className="space-y-2">
                        <div className="flex items-center text-sm text-muted-foreground">
                            <CalendarClock className="mr-2 h-4 w-4" />
                            {t('updated_at')}
                        </div>
                        <div className="text-sm">
                            {formatDate(order.updated_at)}
                        </div>
                    </div>

                    {order.finished_at && (
                        <div className="space-y-2">
                            <div className="flex items-center text-sm text-muted-foreground">
                                <CalendarCheck className="mr-2 h-4 w-4" />
                                {t('finished_at')}
                            </div>
                            <div className="text-sm">
                                {formatDate(order.finished_at)}
                            </div>
                        </div>
                    )}
                    <div className="space-y-2">
                        <div className="flex items-center text-sm text-muted-foreground">
                            <Clock className="mr-2 h-4 w-4" />
                            {t('total_time')}
                        </div>
                        <div className="text-sm font-medium">
                            {formatTime(order.total_time_minutes)}
                        </div>
                    </div>
                </div>

                {/* Images Gallery */}
                {order.images && order.images.length > 0 && (
                    <div className="space-y-2">
                        <div className="flex items-center text-sm font-medium">
                            <ImageIcon className="mr-2 h-4 w-4" />
                            {t('attached_images')} ({order.images.length})
                        </div>
                        <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                            {order.images.map((image) => (
                                <a
                                    key={image.id}
                                    href={image.url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="group relative aspect-square overflow-hidden rounded-lg border bg-muted"
                                >
                                    <img
                                        src={image.url}
                                        alt={image.name}
                                        className="h-full w-full object-cover transition-transform group-hover:scale-105"
                                    />
                                </a>
                            ))}
                        </div>
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
