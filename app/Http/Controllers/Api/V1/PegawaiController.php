<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PegawaiController extends Controller
{
    
    protected $user = null;

    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
    }

    public function store(Request $req){
        
        $validator = Validator::make($req->all(),[
            'peg_nip' => 'required',
            'peg_nama' => 'required',
            'peg_namauser' => 'required|unique:pegawai,peg_namauser',
            'peg_email' => 'required|email',
            'peg_isactive' => 'required',
            'passw'=>'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/',
            'usrgroup'=>'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $pegawai = Pegawai::create([
            'peg_nama'=> strtolower($req->peg_nama),
            'peg_nip'=>$req->peg_nip,
            'peg_nik'=>$req->peg_nik,
            'peg_namauser'=>$req->peg_namauser,
            'passw'=> Hash::make($req->passw),
            'peg_alamat'=>$req->peg_alamat,
            'peg_telepon'=>$req->peg_telepon,
            'peg_email'=>$req->peg_email,
            'peg_pangkat'=>$req->peg_pangkat,
            'peg_jabatan'=>$req->peg_jabatan,
            'peg_golongan'=>$req->peg_golongan,
            'peg_no_pbj'=>$req->peg_no_pbj,
            'peg_no_sk'=>$req->peg_no_sk,
            'peg_isactive'=>$req->peg_isactive,
            'audituser'=>(isset($this->user))? $this->user['user']:null,
            'usrgroup'=>$req->usrgroup
        ]);

        //return response JSON user is created
        if($pegawai) {
            return response()->json([
                'success' => true,
                'user'    => $pegawai,  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function store_update($peg_id,Request $req){
        if(Auth::guard('pegawai')->check()){
            $user = Auth::guard('pegawai')->user();
        }else{
            return response()->json('no auth',422);
        }        
        $validator = Validator::make($req->all(),[
            'peg_email' => 'email',
            'passw'=>'min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }        
        $pegawai = Pegawai::findOrFail($peg_id);
        $data = $req->all();
        ($req->passw) ? $data['passw'] = Hash::make($req->passw) : '';
        ($req->peg_nama) ? $data['peg_nama'] = strtolower($req->peg_nama) : '';
        $data['audituser'] = (isset($this->user))? $this->user['user']:null;
        $pegawai->update($data);
        
        if($pegawai) {
            return response()->json([
                'success' => true,
                'user'    => $pegawai,  
            ], 201);
        }
        
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function pegawai($peg_id){
        $pegawai = Pegawai::findOrFail($peg_id);
        if($pegawai){
            return response()->json($pegawai,201);
        }
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function list_user(Request $req){
        $pegawai = new Pegawai();
        $l = 10;
        $total = $pegawai->count();
        $path = URL::current();
        if($req->length){
            $l = $req->length;
            $path.='?length='.$l;
        }
        $list = $pegawai->paginate($l);
        if($req->q){
            $q = $req->q;
            $list = $pegawai->where('peg_nama','LIKE',"%{$q}%")
            ->orWhere('peg_nip','LIKE',"%{$q}%")
            ->orWhere('peg_namauser','LIKE',"%{$q}%")->paginate($l);
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $list->withPath(url($path));
        return response()->json([
            'success'=>true,
            'total'=>$total,
            'data'=>$list
        ],201);
    }
    
    public function list_userd($usgrp,Request $req){
        $pegawai = Pegawai::where('usrgroup',$usgrp);
        $l = 10;
        $total = $pegawai->count();
        $path = URL::current();
        if($req->length){
            $l = $req->length;
            $path.='?length='.$l;
        }
        $list = $pegawai->paginate($l);
        if($req->q){
            $q = $req->q;
            $list = $pegawai->where('peg_nama','LIKE',"%{$q}%")
            ->orWhere('peg_nip','LIKE',"%{$q}%")
            ->orWhere('peg_namauser','LIKE',"%{$q}%")->paginate($l);
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $list->withPath(url($path));
        return response()->json([
            'success'=>true,
            'total'=>$total,
            'data'=>$list
        ],201);
    }
}
