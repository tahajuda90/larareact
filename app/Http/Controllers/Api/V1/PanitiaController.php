<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Panitia;
use App\Models\AnggotaPnt;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\AuthController;

class PanitiaController extends Controller
{
    //
    protected $user = null;
    
    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
    }
    
    
    public function panitia_store(Request $req){
        $validator = Validator::make($req->all(),[]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $panitia = Panitia::create([
            'audituser'=>(isset($this->user))?$this->user['user']:'admin',
            'pnt_nama'=>$req->pnt_nama,
            'pnt_tahun'=>$req->pnt_tahun,
            'pnt_no_sk'=>$req->pnt_no_sk,
            'pnt_telp'=>$req->pnt_telp,
            'pnt_email'=>$req->pnt_email,
            'pnt_alamat'=>$req->pnt_alamat,
            'is_active'=>$req->is_active
//'id_ketua_ukpbj'=>$req->id_ketua_ukpbj,
        ]);
        //return response JSON user is created
        if($panitia) {
            return response()->json([
                'success' => true,
                'data'    => $panitia,  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function panitia_update($pnt_id,Request $req){
        $panitia = Panitia::findOrFail($pnt_id);
        $validator = Validator::make($req->all(),[]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $data = $req->all();
        $data['audituser'] = (isset($this->user))?$this->user['user']:'admin';
        $panitia->update($data);
        if($panitia) {
            return response()->json([
                'success' => true,
                'user'    => $panitia,  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function panitia($pnt_id){
        $panitia = Panitia::with('pegawai')->where('pnt_id',$pnt_id)->first();
        if($panitia) {
            return response()->json([
                'success' => true,
                'data'    => $panitia 
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function list_panitia(Request $req){
        $panitia = Panitia::withCount('anggota');
        $l = 10;
        $total = $panitia->count();
        $path = 'v1/paniitia';
        if($req->length){
            $l = $req->length;
            $path.='?length='.$l;
        }
        $list = $panitia->paginate($l);
        if($req->q){
            $q = $req->q;
            $list = $panitia->where('pnt_nama','LIKE',"%{$q}%")
            ->orWhere('pnt_no_sk','LIKE',"%{$q}%")->paginate($l);
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $list->withPath(url($path));
        return response()->json([
            'success'=>true,
            'total'=>$total,
            'data'=>$list
        ],201);
    }
    
//    public function list_anggota($pnt_id){
//        $panitia = Panitia::findOrFail($pnt_id);
//        $anggota = AnggotaPnt::where('pnt_id',$panitia->pnt_id)->get();
//        return response()->json([
//            'success'=>true,
//            'data'=>$anggota
//        ],201);
//    }
    
    public function tambah_anggota($pnt_id,Request $req){
        $panitia = Panitia::findOrFail($pnt_id);
        $data = [];
        $anggota = false;
        AnggotaPnt::where('pnt_id',$panitia->pnt_id)->delete();
        if (count($req->anggota) > 0) {
            foreach ($req->anggota as $agt) {
//            $anggota = AnggotaPnt::updateOrCreate(['pnt_id'=>$panitia->pnt_id,'peg_id'=>$agt['peg_id']]);
                array_push($data, ['pnt_id' => $panitia->pnt_id, 'peg_id' => $agt['peg_id']]);
            }
            $anggota = AnggotaPnt::insert($data);
        }
        if ($anggota || $panitia) {
            return response()->json([
                        'success' => true,
                        'data' => $data
                            ], 201);
        }
    }
    
    public function hapus_anggota(Request $req){
        $peg_id = $req->peg_id;
        $pnt_id = $req->pnt_id;
        try{
            $anggota = AnggotaPnt::where('pnt_id',$pnt_id)->where('peg_id',$peg_id)->delete();
        }catch (\Illuminate\Database\QueryException $e) {
            //return JSON process insert failed 
            return response()->json([
                        'success' => false,
                        'data' => $e
                            ], 409);
        }
        if ($anggota) {
            return response()->json([
                        'success' => true,
                        'data' => 'Data berhasil dihapus'
                            ], 201);
        }
    }
}
