<?php

namespace InertiaStudio\Laravel\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Inertia\Inertia;
use Inertia\Response;
use InertiaStudio\Laravel\PanelManager;

class AuthController extends Controller
{
    // ─── Login ──────────────────────────────────────

    public function showLogin(): Response
    {
        return Inertia::render('Studio::Login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $panel = app(PanelManager::class)->getCurrentPanel();
        $guard = $panel->guard();

        if (Auth::guard($guard)->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('studio.dashboard'));
        }

        return redirect()->back()->withErrors([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $panel = app(PanelManager::class)->getCurrentPanel();

        Auth::guard($panel->guard())->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('studio.login');
    }

    // ─── Registration ───────────────────────────────

    public function showRegister(): Response|RedirectResponse
    {
        $panel = app(PanelManager::class)->getCurrentPanel();

        if (! $panel->hasRegistration()) {
            return redirect()->route('studio.login');
        }

        return Inertia::render('Studio::Register');
    }

    public function register(Request $request): RedirectResponse
    {
        $panel = app(PanelManager::class)->getCurrentPanel();

        if (! $panel->hasRegistration()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', PasswordRule::defaults(), 'confirmed'],
        ]);

        $modelClass = $panel->userModel() ?? $this->resolveUserModel($panel);

        $user = $modelClass::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        $panel->afterRegistration($user);

        Auth::guard($panel->guard())->login($user);

        $request->session()->regenerate();

        return redirect()->route('studio.dashboard');
    }

    // ─── Forgot Password ────────────────────────────

    public function showForgotPassword(): Response|RedirectResponse
    {
        $panel = app(PanelManager::class)->getCurrentPanel();

        if (! $panel->hasPasswordReset()) {
            return redirect()->route('studio.login');
        }

        return Inertia::render('Studio::ForgotPassword');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $panel = app(PanelManager::class)->getCurrentPanel();

        if (! $panel->hasPasswordReset()) {
            abort(403);
        }

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $broker = $this->passwordBroker($panel);

        $status = $broker->sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->back()->with('success', __($status));
        }

        return redirect()->back()->withErrors([
            'email' => __($status),
        ]);
    }

    // ─── Reset Password ─────────────────────────────

    public function showResetPassword(Request $request, string $token): Response|RedirectResponse
    {
        $panel = app(PanelManager::class)->getCurrentPanel();

        if (! $panel->hasPasswordReset()) {
            return redirect()->route('studio.login');
        }

        return Inertia::render('Studio::ResetPassword', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $panel = app(PanelManager::class)->getCurrentPanel();

        if (! $panel->hasPasswordReset()) {
            abort(403);
        }

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', PasswordRule::defaults(), 'confirmed'],
        ]);

        $broker = $this->passwordBroker($panel);

        $status = $broker->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('studio.login')->with('success', __($status));
        }

        return redirect()->back()->withErrors([
            'email' => __($status),
        ]);
    }

    // ─── Email Verification ─────────────────────────

    public function showVerifyEmail(): Response
    {
        return Inertia::render('Studio::VerifyEmail');
    }

    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            $request->user()->markEmailAsVerified();
            event(new Verified($request->user()));
        }

        return redirect()->route('studio.dashboard')->with('success', 'Email verified.');
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('studio.dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return redirect()->back()->with('success', 'Verification link sent.');
    }

    // ─── Helpers ────────────────────────────────────

    private function resolveUserModel(mixed $panel): string
    {
        $guard = $panel->guard();
        $provider = config("auth.guards.{$guard}.provider", 'users');

        return config("auth.providers.{$provider}.model", \App\Models\User::class);
    }

    private function passwordBroker(mixed $panel): \Illuminate\Auth\Passwords\PasswordBroker
    {
        $guard = $panel->guard();
        $provider = config("auth.guards.{$guard}.provider", 'users');

        return Password::broker($provider === 'users' ? null : $provider);
    }
}
