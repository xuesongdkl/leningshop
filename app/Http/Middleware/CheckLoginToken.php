<?php

namespace App\Http\Middleware;

use Closure;

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
        if(isset($_COOKIE['id'])&& isset($_COOKIE['token'])){
            $key ='str:u:web:'.$_COOKIE['id'];
            $token =Redis::get($key);
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
