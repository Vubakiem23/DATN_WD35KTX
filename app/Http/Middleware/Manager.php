<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\RoleUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Manager
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $role_user = RoleUser::where('user_id', $user->id)->first();
            $roleNames = Role::where('id', $role_user->role_id)->pluck('ten_quyen');
            if ($roleNames->contains('manager')) {
                return $next($request);
            }
            return redirect(route('error'));
        }
        return redirect(route('auth.login'));
    }
}
