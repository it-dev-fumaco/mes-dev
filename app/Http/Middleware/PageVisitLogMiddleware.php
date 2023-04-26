<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Auth;

class PageVisitLogMiddleware
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
        DB::connection('mysql_mes')->beginTransaction();
        try {
            $log = DB::connection('mysql_mes')->table('page_visit_logs')->where('url', request()->route()->uri)->first();
            $user = Auth::check() ? Auth::user()->email : request()->ip();
            $val = [
                'no_of_visit' => $log ? $log->no_of_visit + 1 : 1,
                'last_transaction_by' => $user
            ];

            if($log){
                DB::connection('mysql_mes')->table('page_visit_logs')->where('url', request()->route()->uri)->update($val);
            }else{
                $val['url'] = request()->route()->uri;
                DB::connection('mysql_mes')->table('page_visit_logs')->insert($val);
            }
            
            DB::connection('mysql_mes')->commit();
        } catch (\Throwable $th) {
            DB::connection('mysql_mes')->rollback();
        }

        return $next($request);
    }
}
