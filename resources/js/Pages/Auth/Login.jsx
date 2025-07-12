import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

import TopLeftSVG from '@/Components/TopLeftSVG';
import BottomLeftSVG from '@/Components/BottomLeftSVG';
import TopRightSVG from '@/Components/TopRightSVG';
import BottomRightSVG from '@/Components/BottomRightSVG';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        phone: '',
        password: '',
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

            {status && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            <div className="absolute top-0 left-0 p-4">
                <TopLeftSVG className="w-32 h-32" />
            </div>

            <div className="absolute bottom-0 left-0 p-4">
                <BottomLeftSVG className="w-32 h-32" />
            </div>

            <div className="absolute top-0 right-0 p-4">
                <TopRightSVG className="w-32 h-32"  />
            </div>

            <div className="absolute bottom-0 right-0 p-4">
                <BottomRightSVG className="w-32 h-32" />
            </div>

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="phone" value="Phone" />

                    <TextInput
                        id="phone"
                        type="tel"
                        name="phone"
                        value={data.phone}
                        className="mt-1 block w-full border-2"
                        style={{ borderColor: '#E3B911' }}
                        autoComplete="username"
                        isFocused={true}
                        onChange={(e) => setData('phone', e.target.value)}
                    />

                    <InputError message={errors.phone} className="mt-2" />
                </div>

                <div className="mt-4">
                    <div className="flex items-center justify-between">
                        <InputLabel htmlFor="password" value="Password" />
                        {canResetPassword && (
                            <Link
                                href={route('password.request')}
                                className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none"
                            >
                                Forgot password?
                            </Link>
                        )}
                    </div>

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full border-2"
                        style={{ borderColor: '#E3B911' }}
                        autoComplete="current-password"
                        onChange={(e) => setData('password', e.target.value)}
                    />

                    <InputError message={errors.password} className="mt-2" />
                </div>

                {/* <div className="mt-4 block">
                    <label className="flex items-center">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(e) =>
                                setData('remember', e.target.checked)
                            }
                        />
                        <span className="ms-2 text-sm text-gray-600">
                            Remember me
                        </span>
                    </label>
                </div> */}

                <div className="mt-4 flex items-center justify-between">
                    <div>
                        <span className="text-sm text-gray-600">Don't have an account?</span>
                        <Link
                            href={route('register')}
                            className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none ms-1"
                        >
                            Register
                        </Link>
                    </div>
                    <PrimaryButton className="ml-4" disabled={processing}>
                        Log in
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
