<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function user() {
        if (Auth::guard('pegawai')->check()) {
            $peg = Auth::guard('pegawai')->user();
            return ['user_id'=>$peg->peg_id,'nama'=>$peg->peg_nama,'email'=>$peg->peg_email,'user'=>$peg->peg_namauser,'role'=>$peg->usrgroup];
        } else if (Auth::guard('rekanan')->check()) {
            $rkn = Auth::guard('rekanan')->user();
            return ['user_id'=>$rkn->rkn_id,'nama'=>$rkn->rkn_nama,'email'=>$rkn->rkn_email,'user'=>$rkn->rkn_namauser,'role'=>'RKN'];
        }
    }

    public function login(Request $req){
        if($token = Auth::guard('pegawai')->attempt(['peg_namauser' => $req->username, 'password' =>$req->password])){
            return $this->respondWithToken($token,'pegawai');
        }else if($token = Auth::guard('rekanan')->attempt(['rkn_namauser' => $req->username, 'password' =>$req->password,'rkn_status_verifikasi'=>'verif'])){
            return $this->respondWithToken($token,'rekanan');
        }
        return response()->json(['error'=>'Unauthorized'],401);
    }
    
    public function me(){
        return response()->json($this->user(),201);
//        return JWTAuth::parseToken()->authenticate();
    }
    
    public function logout(){
        if(Auth::guard('pegawai')->check()) Auth::guard('pegawai')->logout();
        if(Auth::guard('rekanan')->check()) Auth::guard('rekanan')->logout ();
        return response()->json(['message'=>'Sukses logout'],201);
    }

    protected function respondWithToken($token,$jnsuser){
        $role = ($jnsuser === 'pegawai') ? auth($jnsuser)->user()->usrgroup : 'RKN';
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth($jnsuser)->factory()->getTTL()*60,
            'role'=>$role
        ],201);
    }
}
