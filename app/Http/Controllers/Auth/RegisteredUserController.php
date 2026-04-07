<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminWhitelist;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // We remove 'role' from validation because we determine it ourselves now
            'admin_code' => ['nullable', 'string'],
        ]);

        /**
         * ROLE LOGIC (two paths to admin):
         * 1. Email is on the admin whitelist (pre-approved by an existing admin).
         * 2. They supply the secret registration code as a fallback.
         * Otherwise, the user is registered as a student.
         */
        $isWhitelisted = AdminWhitelist::where('email', $request->email)->exists();
        $role = ($isWhitelisted || $request->admin_code === 'TAMUT-ADMIN-2026') ? 'admin' : 'student';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $route = 'student.dashboard';
        if ($user->role == 'admin') {
            $route = 'admin.dashboard';
        }

        return redirect(route($route, absolute: false));
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }
}
