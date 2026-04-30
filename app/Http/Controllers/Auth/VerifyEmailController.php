<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $id = $request->route('id');
        $hash = $request->route('hash');

        if ($request->user()) {
            $user = $request->user();

            if ($user->hasVerifiedEmail()) {
                return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
            }

            if (hash_equals($hash, sha1($user->getEmailForVerification()))) {
                if ($user->markEmailAsVerified()) {
                    event(new Verified($user));
                }
                return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
            }

            return redirect()->route('verification.notice')->with('error', 'Invalid verification link.');
        }

        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Email already verified. Please log in.');
        }

        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('login')->with('status', 'Email verified successfully! Please log in with your credentials.');
    }
}
