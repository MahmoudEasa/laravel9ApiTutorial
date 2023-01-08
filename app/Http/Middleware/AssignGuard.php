<?php

namespace App\Http\Middleware;

use App\Traits\GeneralTrait;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;
// use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
// use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;

class AssignGuard extends BaseMiddleware
{
    use GeneralTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if ($guard != null){
            auth()->shouldUse($guard);
            $token = $request->header('auth-token');
            $request->headers->set('auth-token', (string) $token, true);
            $request->headers->set('Authorization', 'Bearer '.$token, true);
            try{
                // $user = $this->auth->authenticate($request);
                $user = JWTAuth::parseToken()->authenticate();
            }catch(TokenExpiredException $ex){
                return $this->returnError('','Unauthenticated user');
            }catch(JWTException $ex){
                return $this->returnError('','token_invalid', $ex->getMessage());
            }
        }
        return $next($request);
    }
}