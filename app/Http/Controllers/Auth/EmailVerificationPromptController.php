<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        // Default route is to standard dashboard
        $route = 'student.dashboard';
        $user = User::where('email', $request->email)->first();

        // If the user is an admin, reroute them to the admin dashboard
        if ($user->role == 'admin') {
            $route = 'admin.dashboard';
        }

        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(route($route, absolute: false))
            : view('auth.verify-email');
    }
}
