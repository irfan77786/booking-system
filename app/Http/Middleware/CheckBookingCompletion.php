<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckBookingCompletion
{
    

    public function handle(Request $request, Closure $next)
    {
         
         
         
         
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
