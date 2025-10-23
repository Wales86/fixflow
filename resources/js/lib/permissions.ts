import { User } from '@/types';

export function hasPermission(user: User | undefined, permission: string): boolean {
    return user?.permissions?.includes(permission) ?? false;
}
