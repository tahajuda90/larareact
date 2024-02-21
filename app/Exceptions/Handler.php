<?php

namespace App\Exceptions;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    
//    public function render($request, Throwable $e) {
//        try{
//            JWTAuth::parseToken()->authenticate();
//        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
//            return response()->json(['error'=>'Token Expired'],401);
//        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
//            return response()->json(['error'=>'Token Invalid'],401);
//        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e){
//            return response()->json(['error'=>'Token not provided'],401);
//        }
//    }
}
