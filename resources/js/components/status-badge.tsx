import { Badge } from '@/components/ui/badge';
interface StatusBadgeProps {
    status: App.Enums.RepairOrderStatus;
    size?: 'default' | 'lg';
}

const statusConfig: Record<
    App.Enums.RepairOrderStatus,
    { label: string; className: string }
> = {
    new: {
        label: 'Nowe',
        className:
            'border-transparent bg-gray-100 text-gray-900 hover:bg-gray-100/80 dark:bg-gray-800 dark:text-gray-50 dark:hover:bg-gray-800/80',
    },
    diagnosis: {
        label: 'Diagnoza',
        className:
            'border-transparent bg-blue-100 text-blue-800 hover:bg-blue-100/80 dark:bg-blue-900/20 dark:text-blue-400',
    },
    awaiting_contact: {
        label: 'Wymaga kontaktu',
        className:
            'border-transparent bg-yellow-100 text-yellow-800 hover:bg-yellow-100/80 dark:bg-yellow-900/20 dark:text-yellow-400',
    },
    awaiting_parts: {
        label: 'Czeka na części',
        className:
            'border-transparent bg-orange-100 text-orange-800 hover:bg-orange-100/80 dark:bg-orange-900/20 dark:text-orange-400',
    },
    in_progress: {
        label: 'W naprawie',
        className:
            'border-transparent bg-indigo-100 text-indigo-800 hover:bg-indigo-100/80 dark:bg-indigo-900/20 dark:text-indigo-400',
    },
    ready_for_pickup: {
        label: 'Gotowe do odbioru',
        className:
            'border-transparent bg-green-100 text-green-800 hover:bg-green-100/80 dark:bg-green-900/20 dark:text-green-400',
    },
    closed: {
        label: 'Zamknięte',
        className:
            'border-transparent bg-gray-100 text-gray-900 hover:bg-gray-100/80 dark:bg-gray-800 dark:text-gray-50 dark:hover:bg-gray-800/80',
    },
};

export function StatusBadge({ status, size = 'default' }: StatusBadgeProps) {
    const config = statusConfig[status] ?? statusConfig.new;

    const sizeClasses = {
        default: '',
        lg: 'px-3 py-1 text-sm',
    };

    return (
        <Badge className={`${config.className} ${sizeClasses[size]}`}>
            {config.label}
        </Badge>
    );
}
