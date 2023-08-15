<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Gate;
use Closure;
use DB;

class AuthGates
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = \Auth::user();
        if (!app()->runningInConsole() && $user) {
            $role_permissions = DB::connection('mysql_mes')->table('role_permissions')
                ->select('user_group_id', 'permission')->get();

            $permissionsArray = [];

			foreach ($role_permissions as $permission) {
				$permissionsArray[$permission->permission][] = $permission->user_group_id;
			}

			$user_roles = DB::connection('mysql_mes')->table('user')
				->where('user_access_id', $user->user_id)->whereNotNull('user_group_id')
				->distinct()->pluck('user_group_id')->toArray();

			foreach ($permissionsArray as $title => $roles) {
                Gate::define($title, function () use ($roles, $user_roles) {
                    return count(array_intersect($user_roles, $roles)) > 0;
                });
            }
        }

        return $next($request);
    }
}