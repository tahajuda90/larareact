<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            JWTAuth::parseToken()->authenticate();
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
            return response()->json(['error'=>'Token Expired'],401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
            return response()->json(['error'=>'Token Invalid'],401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e){
            return response()->json(['error'=>'Token not provided'],401);
        }
        return $next($request);
    }
}
