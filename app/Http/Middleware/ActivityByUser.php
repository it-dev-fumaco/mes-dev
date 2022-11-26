<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Cache;
use DB;
use Carbon\Carbon;

class ActivityByUser
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
        if (Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(10); // keep online for 1 min
            Cache::put('user-is-online-' . Auth::user()->user_id, true, $expiresAt);
            // last seen
            DB::connection('mysql_mes')->table('user')->where('user_access_id', Auth::user()->user_id)->update(['last_seen' => (new \DateTime())->format("Y-m-d H:i:s")]);
        }

        return $next($request);
    }
}
