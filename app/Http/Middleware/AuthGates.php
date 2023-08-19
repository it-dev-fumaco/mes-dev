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
            $department = DB::connection('mysql_essex')->table('departments')->where('department_id', $user->department_id)->pluck('department')->first();
            $is_admin = $department && $department == 'Information Technology' ? 1 : 0;

			$permissionsArray = [];
			if ($is_admin) {
				$role_permissions_array = [
					'manage-workstations',
					'manage-machines',
					'manage-rescheduled-delivery-reason',
					'manage-production-order-cancellation',
					'manage-shifts',
					'manage-item-classification-source',
					'manage-fast-issuance-permission',
					'manage-wip-warehouse',
					'manage-users',
					'manage-user-groups',
					'manage-email-notifications',
					'manage-role-permissions',
					'reports',
					'view-incoming-orders',
					'create-production-order',
					'cancel-production-order',
					'close-production-order',
					'override-production-order',
					'reopen-production-order',
					'create-production-order-feedback',
					'cancel-production-order-feedback',
					'reschedule-delivery-date-order',
					'create-withdrawal-slip',
					'print-withdrawal-slip',
					'change-production-order-items',
					'fast-issue-items',
					'return-items-to-warehouse',
					'add-production-order-items',
					'create-material-request',
					'assign-shift-schedule',
					'reschedule-delivery-date-production-order',
					'assign-production-order-schedule',
					'assign-production-order-to-machines',
					'assign-bom-process',
					'print-job-ticket',
					'edit-operator-timelog',
					'reset-operator-timelog',
					'override-operator-timelog',
					'update-wip-production-order-process',
				];

				$role_permissions = [];
				foreach($role_permissions_array as $permission) {
					$role_permissions[] = [
						'permission' => $permission,
						'user_group_id' => $user->user_group_id
					];

					$role_permissions = collect($role_permissions);
				}
				
				foreach ($role_permissions as $permission) {
					$permissionsArray[$permission['permission']][] = $permission['user_group_id'];
				}
			} else {
				$role_permissions = DB::connection('mysql_mes')->table('role_permissions')
                	->select('user_group_id', 'permission')->get()->toArray();

				foreach ($role_permissions as $permission) {
					$permissionsArray[$permission->permission][] = $permission->user_group_id;
				}
			}

			$user_roles = DB::connection('mysql_mes')->table('user')
				->where('user_access_id', $user->user_id)->whereNotNull('user_group_id')
				->distinct()->pluck('user_group_id')->toArray();

			foreach ($permissionsArray as $title => $roles) {
                Gate::define($title, function () use ($roles, $user_roles, $is_admin) {
                    return count(array_intersect($user_roles, $roles)) > 0 || $is_admin;
                });
            }
        }

        return $next($request);
    }
}
