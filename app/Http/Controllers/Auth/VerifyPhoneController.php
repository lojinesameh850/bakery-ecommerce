<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
// use Illuminate\Foundation\Auth\VerifiesPhones;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyPhoneController extends Controller
{
    // use VerifiesPhones;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the phone verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedPhone()
                        ? redirect($this->redirectPath())
                        : view('auth.verify-phone');
    }

    /**
     * Mark the authenticated user's phone number as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        if (! hash_equals((string) $request->route('id'), (string) $request->user()->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->route('hash'), sha1($request->user()->getPhoneForVerification()))) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedPhone()) {
            return $request->wantsJson()
                        ? new JsonResponse([], 204)
                        : redirect($this->redirectPath());
        }

        if ($request->user()->markPhoneAsVerified()) {
            event(new Verified($request->user()));
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect($this->redirectPath())->with('verified', true);
    }

    /**
     * Resend the phone verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedPhone()) {
            return $request->wantsJson()
                        ? new JsonResponse([], 204)
                        : redirect($this->redirectPath());
        }

        $request->user()->sendPhoneVerificationNotification();

        return $request->wantsJson()
                    ? new JsonResponse([], 202)
                    : back()->with('resent', true);
    }

    /**
     * Mark the authenticated user's phone number as verified (alternative method for frontend).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyPhone(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedPhone()) {
            return redirect()->intended(
                config('app.frontend_url').'/verify-registration?verified=1'
            );
        }

        if ($request->user()->markPhoneAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(
            config('app.frontend_url').'/verify-registration?verified=1'
        );
    }

    public function __invoke(Request $request)
    {
        return $this->verify($request);
    }
}