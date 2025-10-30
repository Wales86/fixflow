import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import AppLogoIconTools from './app-logo-icon-tools';

export default function AppLogo() {
    const { auth, name: appName } = usePage<SharedData>().props;
    const user = auth.user;

    return (
        <>
            <div className="flex aspect-square size-8 items-center justify-center rounded-md">
                <AppLogoIconTools className="size-9 fill-current text-[var(--foreground)] dark:text-white" />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="truncate leading-tight font-semibold text-muted-foreground">
                    {appName}
                </span>
                <span className="truncate text-sm leading-tight">
                    {user?.workshop.name}
                </span>
            </div>
        </>
    );
}
