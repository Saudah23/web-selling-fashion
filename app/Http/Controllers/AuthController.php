<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
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

    public function sendPasswordResetEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan dalam sistem kami.']);
        }

        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($request->email));

        Mail::send([], [], function ($message) use ($user, $resetUrl) {
            $message->to($user->email, $user->name)
                ->subject('Reset Password - FASHION SAAZZ')
                ->html('
                    <div style="font-family:Arial,sans-serif;max-width:600px;margin:auto;padding:20px;">
                        <h2 style="color:#2c3e50;">FASHION <span style="color:#ff6b6b;">SAAZZ</span></h2>
                        <p>Halo <strong>' . $user->name . '</strong>,</p>
                        <p>Kami menerima permintaan reset password untuk akun Anda.</p>
                        <p style="text-align:center;margin:30px 0;">
                            <a href="' . $resetUrl . '" style="background:#ff6b6b;color:white;padding:12px 30px;border-radius:6px;text-decoration:none;font-weight:bold;">
                                Reset Password
                            </a>
                        </p>
                        <p>Atau salin link berikut ke browser Anda:</p>
                        <p style="word-break:break-all;color:#666;">' . $resetUrl . '</p>
                        <p>Link ini berlaku selama 60 menit. Jika Anda tidak meminta reset password, abaikan email ini.</p>
                        <hr>
                        <p style="color:#999;font-size:12px;">FASHION SAAZZ - Toko Fashion Online Terpercaya</p>
                    </div>
                ');
        });

        return back()->with('status', 'Link reset password telah dikirim ke email ' . $request->email . '. Silakan cek inbox Anda.');
    }

    public function showPasswordResetForm(Request $request, $token)
    {
        return view('auth.passwords.reset', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah kadaluarsa.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
    }
}