<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
   public function handle($request, Closure $next, $role, $permission=null)
    {
        if (Auth::guest()) {
            return redirect('/auth/login');
        }

        $role = is_array($role)
            ? $role
            : explode('|', $role);

        if (! $request->user()->hasAnyRole($role)) {
            return back();
        }

        if ($permission && ! $request->user()->can($permission)) {
            // abort(403);
            return back();
        }

        return $next($request);
    }
}