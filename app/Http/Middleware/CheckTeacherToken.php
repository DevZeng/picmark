<?php

namespace App\Http\Middleware;

use Closure;

class CheckTeacherToken
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
        $code = $request->get('token');
        if (empty($code)){
            return response()->json([
                'code'=>'ERROR',
                'message'=>'参数错误！'
            ],401);
        }
        $teacher = getTeacherToken($code);
        if ($teacher){
            return $next($request);
        }else{
            return response()->json([
                'code'=>'ERROR',
                'message'=>'登录已过期！'
            ],403);
        }
    }
}
