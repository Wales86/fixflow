import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import clients from '@/routes/clients';
import mechanics from '@/routes/mechanics';
import repairOrders from '@/routes/repair-orders';
import reports from '@/routes/reports';
import users from '@/routes/users';
import vehicles from '@/routes/vehicles';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import {
    BarChart3,
    Car,
    LayoutGrid,
    UserCog,
    Users,
    UsersRound,
    Wrench,
} from 'lucide-react';
import AppLogo from './app-logo';

export function AppSidebar() {
    const { t } = useLaravelReactI18n();

    // Wszystkie możliwe NavItems w jednej tablicy z permissions
    const mainNavItems: NavItem[] = [
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
            href: reports.index(),
            icon: BarChart3,
            permission: 'view_reports',
        },
    ];

    const footerNavItems: NavItem[] = [];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
