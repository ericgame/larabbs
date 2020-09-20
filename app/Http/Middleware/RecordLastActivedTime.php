<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RecordLastActivedTime
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
        //如果是登入會員
        if(Auth::check()){
            //記錄會員最後登入時間(最後活躍時間)
            Auth::user()->recordLastActivedAt();
        }

        return $next($request);
    }
}
