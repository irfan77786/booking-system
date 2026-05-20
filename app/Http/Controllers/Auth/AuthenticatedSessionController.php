<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
        $login_type = $request->input('login_type');

        if ($request->type == 'guest') {
            session()->put('booker_first_name', $request->first_name);
            session()->put('booker_last_name', $request->last_name);
            session()->put('booker_email', $request->email);
            session()->put('guest', [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'number' => $request->number,
            ]);
        } else {
            $request->authenticate();
            $request->session()->regenerate();
        }

        return redirect()->intended(
            $login_type == 'booking'
                ? route('submit.passenger.info', absolute: false)
                : route('booking', absolute: false)
        );
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
