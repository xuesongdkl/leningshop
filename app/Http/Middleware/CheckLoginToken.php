<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class CheckLoginToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request,Closure $next){
        if(isset($_COOKIE['uid'])&& isset($_COOKIE['token'])){
            $key ='str:u:token:'.$_COOKIE['uid'];
            $token =redis::hget($key,'web');
            if($_COOKIE['token']==$token){
                $request->attributes->add(['is_login'=>1]);
            }else{
                $request->attributes->add(['is_login'=>0]);
            }
        }else{
            $request->attributes->add(['is_login'=>0]);
        }
        return $next($request);
    }
}
