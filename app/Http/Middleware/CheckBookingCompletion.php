<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckBookingCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Let a fresh home / banner booking POST through: saveBookingFormSession
        // clears booking data and sets booking_completed => false. Otherwise this
        // middleware flushed+redirected before the controller ever ran, blocking
        // a second trip after a completed booking.
        if (
            $request->routeIs('save.booking.form.session')
            && $request->isMethod('post')
        ) {
            return $next($request);
        }

        if (session('booking_completed')) {
            session()->flush();
            $request->session()->regenerateToken();
            session(['booking_completed' => true]);
            return redirect()->route('booking');
        }
        return $next($request);
    }
}
