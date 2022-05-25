<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
class checkLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request  $request, Closure $next, $guard = 'null')
    {
        if($guard != null){
            auth()->shouldUse($guard); //shoud you user guard / table
            $token = $request->header('auth-token');
            $request->headers->set('auth-token', (string) $token, true);
            $request->headers->set('Authorization', 'Bearer '.$token, true);
            try {
                //$user = $this->auth->authenticate($request);  //check authenticted user
               $user = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException $e) {
                return  response()->json(['401','Unauthenticated user']);
            } catch (JWTException $e) {

                return  response()->json(['', 'token_invalid'.$e->getMessage()]);
            }

        }


        return $next($request);
    }
}
