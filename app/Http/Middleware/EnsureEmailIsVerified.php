<?php

namespace App\Http\Middleware;

use Closure;

class EnsureEmailIsVerified
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
        /*
            *對於已經登入但是email未驗證的用戶，顯示email未驗證訊息:(共有3個判斷)
            1.如果用戶已經登入
            2.並且還未驗證Email
            3.並且訪問的不是 email驗證的相關網址 或 登出的網址
        */
        if($request->user() && !$request->user()->hasVerifiedEmail() && !$request->is('email/*', 'logout')){
            //根據客戶端返回對應的內容
            return $request->expectsJson() ? abort(403, 'Your email address is not verified.') : redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
