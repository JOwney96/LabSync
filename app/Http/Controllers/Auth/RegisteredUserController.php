<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
            'role' => ['required', 'in:student,faculty,admin'],
            'faculty_code' => ['nullable', 'string'],
            'admin_code' => ['nullable', 'string'],
        ]);

        /** * ROLE LOGIC & CODE VERIFICATION
         */
        $selectedRole = $request->role;

        // Verify Faculty Code
        if ($selectedRole === 'faculty' && $request->faculty_code !== 'FAC-2026') {
            throw ValidationException::withMessages([
                'faculty_code' => 'The provided faculty access code is incorrect.',
            ]);
        }

        // Verify Admin Code
        if ($selectedRole === 'admin' && $request->admin_code !== 'TAMUT-ADMIN-2026') {
            throw ValidationException::withMessages([
                'admin_code' => 'The provided admin access code is incorrect.',
            ]);
        }

        // Create the User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        /**
         * SPATIE ASSIGNMENT
         * This links the user to the permissions system so your Blade tags work!
         */
        $user->assignRole($selectedRole);

        event(new Registered($user));

        Auth::login($user);


        $route = ($selectedRole === 'admin') ? 'admin.dashboard' : 'student.dashboard';

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
