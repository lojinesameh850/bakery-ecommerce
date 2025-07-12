import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

import TopLeftSVG from '@/Components/TopLeftSVG';
import BottomLeftSVG from '@/Components/BottomLeftSVG';
import TopRightSVG from '@/Components/TopRightSVG';
import BottomRightSVG from '@/Components/BottomRightSVG';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        phone: '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e) => {
        e.preventDefault();
        
        console.log('Form submission started');
        console.log('Form data:', data);
        console.log('Route register:', route('register'));
        
        post(route('register'), {
            onStart: () => {
                console.log('Request started');
            },
            onSuccess: (response) => {
                console.log('Success response:', response);
                reset('password', 'password_confirmation');
            },
            onError: (errors) => {
                console.log('Error response:', errors);
            },
            onFinish: () => {
                console.log('Request finished');
            }
        });
    };

    return (
        <GuestLayout>
            <Head title="Register" />

            <div className="absolute top-0 left-0 p-4">
                <TopLeftSVG className="w-32 h-32" />
            </div>

            <div className="absolute bottom-0 left-0 p-4">
                <BottomLeftSVG className="w-32 h-32" />
            </div>

            <div className="absolute top-0 right-0 p-4">
                <TopRightSVG className="w-32 h-32" />
            </div>

            <div className="absolute bottom-0 right-0 p-4">
                <BottomRightSVG className="w-32 h-32" />
            </div>

            {/* Debug info */}
            <div className="mb-4 p-4 bg-gray-100 rounded text-sm">
                <p><strong>Debug Info:</strong></p>
                <p>Processing: {processing ? 'true' : 'false'}</p>
                <p>Errors: {JSON.stringify(errors)}</p>
                <p>Data: {JSON.stringify(data)}</p>
            </div>

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="name" value="Name" />
                    <TextInput
                        id="name"
                        name="name"
                        value={data.name}
                        className="mt-1 block w-full border-2"
                        style={{ borderColor: '#E3B911' }}
                        autoComplete="name"
                        isFocused={true}
                        onChange={(e) => setData('name', e.target.value)}
                        required
                    />
                    <InputError message={errors.name} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="phone" value="Phone" />
                    <TextInput
                        id="phone"
                        type="tel"
                        name="phone"
                        value={data.phone}
                        className="mt-1 block w-full border-2"
                        style={{ borderColor: '#E3B911' }}
                        autoComplete="tel"
                        onChange={(e) => setData('phone', e.target.value)}
                        required
                    />
                    <InputError message={errors.phone} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full border-2"
                        style={{ borderColor: '#E3B911' }}
                        autoComplete="new-password"
                        onChange={(e) => setData('password', e.target.value)}
                        required
                    />
                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="password_confirmation" value="Confirm Password" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        value={data.password_confirmation}
                        className="mt-1 block w-full border-2"
                        style={{ borderColor: '#E3B911' }}
                        autoComplete="new-password"
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        required
                    />
                    <InputError message={errors.password_confirmation} className="mt-2" />
                </div>

                <div className="mt-4 flex items-center justify-between">
                    <div>
                        <span className="text-sm text-gray-600">Already registered?</span>
                        <Link
                            href={route('login')}
                            className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none ms-1"
                        >
                            Log in
                        </Link>
                    </div>
                    <PrimaryButton
                        className="ml-4"
                        disabled={processing}
                        type="submit"
                    >
                        Register
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}