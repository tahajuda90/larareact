<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rekanan;

class AuthRekananController extends Controller
{
     public function user(){
        if(Auth::guard('rekanan')->check()){
            return Auth::guard('rekanan')->user();
        }else{
           return response()->json(['error'=>'Unauthorized'],401); 
        }
    }

    public function login(Request $req){
        // $credentials = request(['peg_namauser','passw']);
        if($token = Auth::guard('rekanan')->attempt(['rkn_namauser' => $req->rkn_namauser, 'password' =>$req->password])){
            return $this->respondWithToken($token,'rekanan');
        }
        $rekanan = Rekanan::where('rkn_namauser',$req->rkn_namauser)->first();
        return response()->json(['error'=>'Unauthorized'],401);
    }

    public function me(){
        return JWTAuth::parsseToken()->authenticate();
    }
    
    public function logout(){
        if(Auth::guard('rekanan')->check()) Auth::guard('rekanan')->logout ();
    }

    protected function respondWithToken($token,$jnsuser){
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth($jnsuser)->factory()->getTTL()*60
        ]);
    }
}
