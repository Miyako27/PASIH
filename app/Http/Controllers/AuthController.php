<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password tidak valid.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($request->user()) {
            try {
                UserActivity::query()->create([
                    'user_id' => $request->user()->id,
                    'type' => 'Autentikasi',
                    'title' => 'Login ke sistem',
                    'detail' => 'Berhasil masuk ke aplikasi PASIH',
                    'route_name' => 'login.attempt',
                    'method' => 'POST',
                ]);
            } catch (\Throwable) {
                // Logging aktivitas tidak boleh mengganggu proses login.
            }
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            try {
                UserActivity::query()->create([
                    'user_id' => $request->user()->id,
                    'type' => 'Autentikasi',
                    'title' => 'Logout dari sistem',
                    'detail' => 'Keluar dari aplikasi PASIH',
                    'route_name' => 'logout',
                    'method' => 'POST',
                ]);
            } catch (\Throwable) {
                // Logging aktivitas tidak boleh mengganggu proses logout.
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
