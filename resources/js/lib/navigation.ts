import {
    BarChart3,
    Car,
    LayoutGrid,
    UserCog,
    Users,
    UsersRound,
    Wrench,
} from 'lucide-react';
import { type NavItem } from '@/types';
import { dashboard } from '@/routes';
import clients from '@/routes/clients';
import mechanics from '@/routes/mechanics';
import repairOrders from '@/routes/repair-orders';
import reports from '@/routes/reports';
import users from '@/routes/users';
import vehicles from '@/routes/vehicles';
import { useLaravelReactI18n } from 'laravel-react-i18n';

type TFunction = ReturnType<typeof useLaravelReactI18n>['t'];

export const getMainNavItems = (t: TFunction): NavItem[] => [
    {
        title: t('dashboard'),
        href: dashboard(),
        icon: LayoutGrid,
        permission: 'view_dashboard',
    },
    {
        title: t('clients'),
        href: clients.index(),
        icon: Users,
        permission: 'view_clients',
    },
    {
        title: t('vehicles'),
        href: vehicles.index(),
        icon: Car,
        permission: 'view_vehicles',
    },
    {
        title: t('repair_orders'),
        href: repairOrders.index(),
        icon: Wrench,
        permission: 'view_repair_orders',
    },
    {
        title: t('active_repair_orders'),
        href: repairOrders.mechanic(),
        icon: Wrench,
        permission: 'view_repair_orders_mechanic',
    },
    {
        title: t('mechanics.title'),
        href: mechanics.index(),
        icon: UserCog,
        permission: 'view_mechanics',
    },
    {
        title: t('users.title'),
        href: users.index(),
        icon: UsersRound,
        permission: 'view_users',
    },
    {
        title: t('reports'),
        href: reports.team(),
        icon: BarChart3,
        permission: 'view_reports',
        items: [
            {
                title: t('team_performance'),
                href: reports.team(),
                permission: 'view_reports',
            },
            {
                title: t('mechanic_performance'),
                href: reports.mechanic(),
                permission: 'view_reports',
            },
        ],
    },
];
