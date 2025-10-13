<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Set login success session
            $request->session()->flash('login_success', true);

            $user = Auth::user();

            // Redirect based on role
            return match($user->role) {
                'owner' => redirect()->intended('/owner/dashboard'),
                'admin' => redirect()->intended('/admin/dashboard'),
                'customer' => redirect()->intended('/customer/dashboard'),
                default => redirect()->intended('/'),
            };
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['owner', 'admin', 'customer'])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        Auth::login($user);

        // Set login success session for new registration
        $request->session()->flash('login_success', true);

        // Redirect based on role
        return match($user->role) {
            'owner' => redirect('/owner/dashboard'),
            'admin' => redirect('/admin/dashboard'),
            'customer' => redirect('/customer/dashboard'),
            default => redirect('/'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Set logout success session
        $request->session()->flash('logout_success', true);

        return redirect('/login');
    }

    public function showPasswordRequestForm()
    {
        return view('auth.passwords.email');
    }
}