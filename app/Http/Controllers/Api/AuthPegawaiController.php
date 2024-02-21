<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthPegawaiController extends Controller
{
    
    
    public function user(){
        if(Auth::guard('pegawai')->check()){
            return Auth::guard('pegawai')->user();
        }else{
           return response()->json(['error'=>'Unauthorized'],401); 
        }
    }

    public function login(Request $req){
        // $credentials = request(['peg_namauser','passw']);
        if($token = Auth::guard('pegawai')->attempt(['peg_namauser' => $req->peg_namauser, 'password' =>$req->password])){
            return $this->respondWithToken($token,'pegawai');
        }
        return response()->json(['error'=>'Unauthorized'],401);
    }

    public function me(){
        return JWTAuth::parsseToken()->authenticate();
    }
    
    public function logout(){
        if(Auth::guard('pegawai')->check()) Auth::guard('pegawai')->logout();
        return response()->json(['message'=>'Sukses logout'],201);
    }

    protected function respondWithToken($token,$jnsuser){
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth($jnsuser)->factory()->getTTL()*60,
            'role'=>auth($jnsuser)->user()->usrgroup
        ],201);
    }
}
