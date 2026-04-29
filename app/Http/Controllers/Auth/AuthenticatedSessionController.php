<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Enums\Role;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        
        return match ($user->role) {
            Role::ADMIN => redirect()->route('admin.dashboard'),

            Role::CONTRACTOR => redirect()->route('user.dashboard'),

            Role::PROVINCIAL_ENGINEER,
            Role::SITE_INSPECTOR,
            Role::ENGINEER_IV,
            Role::SURVEYOR,
            Role::MTQA,
            Role::RESIDENT_ENGINEER,
            Role::ENGINEER_III
                => redirect()->route('reviewer.dashboard'),

            default => abort(403),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
