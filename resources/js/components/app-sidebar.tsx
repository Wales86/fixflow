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
import { getMainNavItems } from '@/lib/navigation';
import { dashboard } from '@/routes';
import { mechanic } from '@/routes/repair-orders';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import AppLogo from './app-logo';
import { type SharedData } from '@/types';

export function AppSidebar() {
    const { t } = useLaravelReactI18n();
    const { auth } = usePage<SharedData>().props;
    const mainNavItems = getMainNavItems(t);

    const footerNavItems: NavItem[] = [];

    // Determine the logo link based on user role
    const logoHref = auth.user?.roles?.includes('Mechanic')
        ? mechanic()
        : dashboard();

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={logoHref} prefetch>
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
