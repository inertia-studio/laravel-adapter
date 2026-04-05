<?php

namespace InertiaStudio\Laravel\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use InertiaStudio\Laravel\PanelManager;

class AuthController extends Controller
{
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

        $manager = app(PanelManager::class);
        $panel = $manager->getCurrentPanel();
        $guard = $panel->guard();

        if (Auth::guard($guard)->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(
                route('studio.dashboard')
            );
        }

        return redirect()->back()->withErrors([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $manager = app(PanelManager::class);
        $panel = $manager->getCurrentPanel();

        Auth::guard($panel->guard())->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('studio.login');
    }
}
