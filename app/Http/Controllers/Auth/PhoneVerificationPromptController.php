<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PhoneVerificationPromptController extends Controller
{
    /**
     * Show the phone number verification prompt page.
     */
    public function __invoke(Request $request): Response|RedirectResponse
    {
        return $request->user()->hasVerifiedPhone()
                    ? redirect()->intended(route('verify-registration', absolute: false))
                    : Inertia::render('auth/verify-phone', ['status' => $request->session()->get('status')]);
    }
}
