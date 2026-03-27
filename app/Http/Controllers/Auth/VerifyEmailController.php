<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Default route is to standard dashboard
        $route = 'student.dashboard';
        $user = User::where('email', $request->email)->first();

        // If the user is an admin, reroute them to the admin dashboard
        if ($user->role == 'admin') {
            $route = 'admin.dashboard';
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route($route, absolute: false) . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route($route, absolute: false) . '?verified=1');
    }
}
