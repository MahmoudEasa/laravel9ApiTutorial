<?php

namespace App\Http\Middleware;

use App\Traits\GeneralTrait;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class CheckAdminToken
{
    use GeneralTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = null;
        try {
            $user = JWTAuth::parseToken()->authenticate();
                //throw an exception
        }
        catch (\Exception $e) {
            if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException){
                return $this->returnError('E3001', 'INVALID_TOKEN');
            }else if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException){
                return $this->returnError('E3001', 'EXPIRED_TOKEN');
            } else{
                return $this->returnError('E3001', 'TOKEN_NOTFOUND');
            }
        }
        catch (\Throwable $e) {
            if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException){
                return $this->returnError('E3001', 'INVALID_TOKEN');
            }else if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException){
                return $this->returnError('E3001', 'EXPIRED_TOKEN');
            } else{
                return $this->returnError('E3001', 'TOKEN_NOTFOUND');
            }
        }

        if (!$user)
            return $this->returnError('E331', trans('Unauthenticated'));

        return $next($request);
    }
}
