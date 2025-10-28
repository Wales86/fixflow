import { useForm } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { FormEventHandler } from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { store, update } from '@/routes/users';
import { toast } from 'sonner';

interface UserFormProps {
    user?: App.Dto.User.UserData;
    roles: Array<{ value: string; label: string }>;
}

type UserFormData = {
    name: string;
    email: string;
    role: string;
    password?: string;
    password_confirmation?: string;
};

export function UserForm({ user, roles }: UserFormProps) {
    const { t } = useLaravelReactI18n();
    const isEditMode = !!user;

    const { data, setData, post, put, processing, errors, clearErrors } =
        useForm<UserFormData>({
            name: user?.name ?? '',
            email: user?.email ?? '',
            role: user?.roles?.[0] ?? '',
            password: '',
            password_confirmation: '',
        });

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEditMode) {
            put(update(user.id).url, {
                preserveScroll: true,
                onError: () => {
                    toast.error(t('an_error_occurred'));
                },
            });
        } else {
            post(store().url, {
                preserveScroll: true,
                onError: () => {
                    toast.error(t('an_error_occurred'));
                },
            });
        }
    };


    return (
        <Card>
            <CardHeader>
                <CardTitle>
                    {isEditMode
                        ? t('users.edit_user_data')
                        : t('users.user_data')}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="space-y-2">
                        <Label htmlFor="name">
                            {t('name')} <span className="text-red-500">*</span>
                        </Label>
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(e) => {
                                setData('name', e.target.value);
                                clearErrors('name');
                            }}
                            disabled={processing}
                            className={errors.name ? 'border-red-500' : ''}
                        />
                        {errors.name && (
                            <p className="text-sm text-red-500">
                                {errors.name}
                            </p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="email">
                            {t('email')} <span className="text-red-500">*</span>
                        </Label>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => {
                                setData('email', e.target.value);
                                clearErrors('email');
                            }}
                            disabled={processing}
                            className={errors.email ? 'border-red-500' : ''}
                        />
                        {errors.email && (
                            <p className="text-sm text-red-500">
                                {errors.email}
                            </p>
                        )}
                    </div>

                    {!isEditMode && (
                        <>
                            <div className="space-y-2">
                                <Label htmlFor="password">
                                    {t('password')}{' '}
                                    <span className="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="password"
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => {
                                        setData('password', e.target.value);
                                        clearErrors('password');
                                    }}
                                    disabled={processing}
                                    className={
                                        errors.password ? 'border-red-500' : ''
                                    }
                                />
                                {errors.password && (
                                    <p className="text-sm text-red-500">
                                        {errors.password}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="password_confirmation">
                                    {t('password_confirmation')}{' '}
                                    <span className="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    value={data.password_confirmation}
                                    onChange={(e) => {
                                        setData(
                                            'password_confirmation',
                                            e.target.value,
                                        );
                                        clearErrors('password_confirmation');
                                    }}
                                    disabled={processing}
                                    className={
                                        errors.password_confirmation
                                            ? 'border-red-500'
                                            : ''
                                    }
                                />
                                {errors.password_confirmation && (
                                    <p className="text-sm text-red-500">
                                        {errors.password_confirmation}
                                    </p>
                                )}
                            </div>
                        </>
                    )}

                    <div className="space-y-2">
                        <Label htmlFor="role">
                            {t('users.role')}{' '}
                            <span className="text-red-500">*</span>
                        </Label>
                        <Select
                            value={data.role}
                            onValueChange={(value) => {
                                setData('role', value);
                                clearErrors('role');
                            }}
                            disabled={processing}
                        >
                            <SelectTrigger
                                id="role"
                                className={errors.role ? 'border-red-500' : ''}
                            >
                                <SelectValue
                                    placeholder={t('users.select_role')}
                                />
                            </SelectTrigger>
                            <SelectContent>
                                {roles.map((role) => (
                                    <SelectItem key={role.value} value={role.value}>
                                        {role.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {errors.role && (
                            <p className="text-sm text-red-500">
                                {errors.role}
                            </p>
                        )}
                    </div>

                    <div className="flex justify-end gap-4">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => window.history.back()}
                            disabled={processing}
                        >
                            {t('cancel')}
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? t('loading') : t('save')}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    );
}
