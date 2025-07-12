import { useState } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function VerifyPhone({ phone, status }) {
    const [verificationCode, setVerificationCode] = useState('');
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const { data, setData, post, processing } = useForm({
        phone: phone,
        verification_code: '',
    });

    const submitVerification = (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');

        if (!verificationCode || verificationCode.length !== 6) {
            setError('Please enter a valid 6-digit verification code');
            return;
        }

        // Update form data
        setData('verification_code', verificationCode);

        // Submit to your Laravel POST route
        post(route('verify-registration.submit'), {
            onSuccess: (page) => {
                setSuccess('Phone verified successfully! Registration complete. Redirecting...');
                // Let Inertia handle the redirect or do it manually
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            },
            onError: (errors) => {
                if (errors.verification_code) {
                    setError(errors.verification_code);
                } else if (errors.phone) {
                    setError(errors.phone);
                } else {
                    setError('Verification failed. Please try again.');
                }
            },
        });
    };

    const resendCode = (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');

        // Call your resend route
        post(route('verification.resend'), {
            data: { phone: phone },
            onSuccess: () => {
                setSuccess('Verification code resent successfully!');
            },
            onError: (errors) => {
                setError('Failed to resend verification code');
            },
        });
    };

    return (
        <GuestLayout>
            <Head title="Phone Verification" />

            <div className="mb-4 text-sm text-gray-600">
                Enter the 6-digit SMS code sent to your phone number ({phone}) to complete your registration.
            </div>

            {status === 'verification-code-sent' && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    A new verification code has been sent to your phone number.
                </div>
            )}

            {error && (
                <div className="mb-4 text-sm font-medium text-red-600">
                    {error}
                </div>
            )}

            {success && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    {success}
                </div>
            )}

            <form onSubmit={submitVerification}>
                <div className="mb-4">
                    <label htmlFor="verification_code" className="block text-sm font-medium text-gray-700">
                        Verification Code
                    </label>
                    <input
                        id="verification_code"
                        type="text"
                        value={verificationCode}
                        onChange={(e) =>
                            setVerificationCode(e.target.value.replace(/\D/g, '').slice(0, 6))
                        }
                        className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-center text-2xl tracking-widest"
                        placeholder="000000"
                        maxLength="6"
                        required
                    />
                </div>

                <div className="flex items-center justify-between">
                    <PrimaryButton
                        type="submit"
                        disabled={processing || verificationCode.length !== 6}
                    >
                        Complete Registration
                    </PrimaryButton>

                    <button
                        type="button"
                        onClick={resendCode}
                        disabled={processing}
                        className="text-sm text-indigo-600 hover:text-indigo-500 underline"
                    >
                        Resend Code
                    </button>
                </div>

                <div className="mt-4 text-center">
                    <Link
                        href={route('register')}
                        className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Back to Registration
                    </Link>
                </div>
            </form>
        </GuestLayout>
    );
}import { useState } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function VerifyPhone({ phone, status }) {
    const [verificationCode, setVerificationCode] = useState('');
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const { data, setData, post, processing } = useForm({
        phone: phone,
        verification_code: '',
    });

    const submitVerification = (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');

        if (!verificationCode || verificationCode.length !== 6) {
            setError('Please enter a valid 6-digit verification code');
            return;
        }

        // Submit to your Laravel POST route
        post(route('verify-registration.submit'), {
            data: {
                phone: phone,
                verification_code: verificationCode
            },
            onSuccess: (page) => {
                setSuccess('Phone verified successfully! Registration complete. Redirecting...');
                // Let Inertia handle the redirect
            },
            onError: (errors) => {
                if (errors.verification_code) {
                    setError(errors.verification_code);
                } else if (errors.phone) {
                    setError(errors.phone);
                } else {
                    setError('Verification failed. Please try again.');
                }
            },
        });
    };

    const resendCode = (e) => {
        e.preventDefault();
        setError('');
        setSuccess('');

        // Call your resend route - Fixed the data passing
        post(route('verification.resend'), {
            data: { phone: phone },
            onSuccess: () => {
                setSuccess('Verification code resent successfully!');
            },
            onError: (errors) => {
                setError('Failed to resend verification code');
            },
        });
    };

    return (
        <GuestLayout>
            <Head title="Phone Verification" />

            <div className="mb-4 text-sm text-gray-600">
                Enter the 6-digit SMS code sent to your phone number ({phone}) to complete your registration.
            </div>

            {status === 'verification-code-sent' && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    A new verification code has been sent to your phone number.
                </div>
            )}

            {error && (
                <div className="mb-4 text-sm font-medium text-red-600">
                    {error}
                </div>
            )}

            {success && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    {success}
                </div>
            )}

            <form onSubmit={submitVerification}>
                <div className="mb-4">
                    <label htmlFor="verification_code" className="block text-sm font-medium text-gray-700">
                        Verification Code
                    </label>
                    <input
                        id="verification_code"
                        type="text"
                        value={verificationCode}
                        onChange={(e) =>
                            setVerificationCode(e.target.value.replace(/\D/g, '').slice(0, 6))
                        }
                        className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-center text-2xl tracking-widest"
                        placeholder="000000"
                        maxLength="6"
                        required
                    />
                </div>

                <div className="flex items-center justify-between">
                    <PrimaryButton
                        type="submit"
                        disabled={processing || verificationCode.length !== 6}
                    >
                        Complete Registration
                    </PrimaryButton>

                    <button
                        type="button"
                        onClick={resendCode}
                        disabled={processing}
                        className="text-sm text-indigo-600 hover:text-indigo-500 underline"
                    >
                        Resend Code
                    </button>
                </div>

                <div className="mt-4 text-center">
                    <Link
                        href={route('register')}
                        className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Back to Registration
                    </Link>
                </div>
            </form>
        </GuestLayout>
    );
}