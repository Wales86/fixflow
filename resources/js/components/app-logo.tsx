import { usePage } from '@inertiajs/react';
import { SharedData } from '@/types';

export default function AppLogo() {
    const { auth, name: appName } = usePage<SharedData>().props;
    const user = auth.user;

    return (
        <>
            <div className="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
                <img src="/img/logo.png" alt="Logo" className="size-5" />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="truncate font-semibold leading-tight text-muted-foreground">
                    {appName}
                </span>
                <span className="truncate text-sm leading-tight">
                    {user?.workshop.name}
                </span>
            </div>
        </>
    );
}
