<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        // Default route is to standard dashboard
        $route = 'student.dashboard';
        $user = User::where('email', $request->email)->first();

        // If the user is an admin, reroute them to the admin dashboard
        if ($user->role == 'admin') {
            $route = 'admin.dashboard';
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route($route, absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
