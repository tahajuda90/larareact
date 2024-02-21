<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Models\Rekanan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\AuthController;

class RekananController extends Controller
{
    
    protected $user = null;
    
    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
    }
    
    public function store(Request $req){
        $validator = Validator::make($req->all(),[
            'rkn_namauser' => 'required|unique:rekanan,rkn_namauser',
            'passw'=>'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/',
            'rkn_nama'=>'required',
            'rkn_npwp'=>'required',
            'rkn_email'=>'required|email',
            'rkn_alamat'=>'required',
            'rkn_telepon'=>'required',
            'rkn_prop'=>'required',
            'rkn_kota'=>'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $rekanan = Rekanan::create([
                    'btu_id' => $req->btu_id,
                    'rkn_namauser' => $req->rkn_namauser,
                    'passw'=>Hash::make($req->passw),
                    'rkn_nama' => strtolower($req->rkn_nama),
                    'rkn_alamat' => $req->rkn_alamat,
                    'rkn_kodepos' => $req->rkn_kodepos,
                    'rkn_prop' => $req->rkn_prop,
                    'rkn_kota' => $req->rkn_kota,
                    'rkn_npwp' => $req->rkn_npwp,
                    'rkn_email'=>$req->rkn_email,
                    'rkn_pkp' => $req->rkn_pkp,
                    'rkn_telepon'=>$req->rkn_telepon,
                    'rkn_fax'=>$req->rkn_fax,
                    'rkn_mobile_phone'=>$req->rkn_mobile_phone,
                    'rkn_tgl_daftar'=>\Carbon\Carbon::now(),
                    'rkn_isactive' => 1,
                    'rkn_status' => 0,
                    'rkn_status_verifikasi' => 'non',
                    'audituser'=>$req->rkn_namauser
        ]);
        //return response JSON user is created
        if($rekanan) {
            return response()->json([
                'success' => true,
                'user'    => $rekanan,  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function store_update($rkn_id,Request $req){
        $validator = Validator::make($req->all(),[
            'rkn_email' => 'email',
            'passw'=>'min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $rekanan = Rekanan::findOrFail($rkn_id);
        $data = $req->all();
        $data['audituser'] = (isset($this->user))? $this->user['user']:null;
        ($req->passw) ? $data['passw'] = Hash::make($req->passw):'';
        ($req->rkn_nama) ? $data['rkn_nama'] = strtolower($req->rkn_nama):'';
//        ($req->rkn_prop) ? $data['rkn_prop'] = $req->rkn_prop:'';
//        ($req->rkn_kota) ? $data['rkn_kota'] = $req->rkn_kota:'';
        $rekanan->update($data);
//        return response JSON user is created
        if($rekanan) {
            return response()->json([
                'success' => true,
                'user'    => $rekanan,  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
            'data'=>$data
        ], 201);
    }
    
    public function rekanan($rkn_id){
        $rekanan = Rekanan::findOrFail($rkn_id);
        if($rekanan) {
            return response()->json([
                'success' => true,
                'user'    => $rekanan,  
            ], 201);
        }
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function list_rekanan(Request $req){
        $rekanan = new Rekanan();
        $l = 10;
        $total = $rekanan->count();
        $path = 'v1/penyedia';
        if($req->length){
            $l = $req->length;
            $path.='?length='.$l;
        }
        $list = $rekanan->with('B_Usaha:btu_id,btu_nama')->paginate($l);
        if($req->q){
            $q = $req->q;
            $list = $rekanan->with('B_Usaha:btu_id,btu_nama')->where('rkn_nama','LIKE',"%{$q}%")
            ->orWhere('rkn_npwp','LIKE',"%{$q}%")
            ->orWhere('rkn_namauser','LIKE',"%{$q}%")->paginate($l);
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
