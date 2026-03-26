<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Check if user has dashboard permission
                if ($user && $user->checkPermissionTo('хянах_самбар')) {
                    return redirect(RouteServiceProvider::HOME);
                } else {
                    // Redirect to delivery/new if user doesn't have dashboard permission
                    return redirect('/delivery/new');
                }
            }
        }

        return $next($request);
    }
}
