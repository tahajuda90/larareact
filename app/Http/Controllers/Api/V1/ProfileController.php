<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Rekanan;
use App\Models\RknIjinUsaha;
use App\Models\RknLndsnHukum;
use App\Models\RknPjk;
use App\Models\RknPglaman;
use App\Models\RknStaf;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Api\AuthController;

class ProfileController extends Controller
{
    // izin usaha
    protected $user = null;
    
    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
    }
    
    public function ius_store($rkn_id,Request $req){
        $rekanan = Rekanan::findOrFail($rkn_id);
        $validator = Validator::make($req->all(),[
            'jni_nama'=>'required',
            'ius_no'=>'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $ijin = RknIjinUsaha::create([
            'jni_nama'=>$req->jni_nama,
            'ius_no'=>$req->ius_no,
            'ius_klasifikasi'=>$req->ius_klasifikasi,
            'ius_instansi'=>$req->ius_instansi,
            'status_berlaku'=>$req->status_berlaku,
            'ius_berlaku'=>$req->ius_berlaku,
            'rkn_id'=>$rekanan->rkn_id,
            'kls_id'=>$req->kls_id,
            'audituser'=>(isset($this->user))? $this->user['user']:'',
            'ius_id_attachment'=>$req->ius_id_attachment
        ]);
        //return response JSON user is created
        if($ijin) {
            return response()->json([
                'success' => true,
                'data'    => $ijin,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function ius_update($ius_id,Request $req){
        $validator = Validator::make($req->all(),[]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $ijin = RknIjinUsaha::findOrFail($ius_id);
        $data = $req->all();
        (isset($this->user))?$data['audituser']= $this->user['user']:'';
        $ijin->update($data);
        //        return response JSON user is created
        if($ijin) {
            return response()->json([
                'success' => true,
                'data'    => $ijin,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
            'data'=>$data
        ], 201);
    }
    
    public function ius($ius_id){
        $ijin = RknIjinUsaha::findOrFail($ius_id);
        if($ijin) {
            return response()->json([
                'success' => true,
                'data'    => $ijin,  
            ], 201);
        }
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function ius_list($rkn_id,Request $req){
        $ijin = RknIjinUsaha::where('rkn_id',$rkn_id);
        $l=10;
        $total = $ijin->count();
        $path = URL::current();
        if($req->length){
            $l = $req->length;
            $path.='?length='.$l;
        }
        $list = $ijin->paginate($l);
        if ($req->q) {
            $q = $req->q;
            $list = $ijin->Where('jni_nama','LIKE',"%{$q}%")
                    ->orWhere('ius_no','LIKE',"%{$q}%")->paginate($l);
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $list->withPath(url($path));
        return response()->json([
            'succes'=>true,
            'total'=>$total,
            'data'=>$list
        ],201);
    }
    
    
    // landasan hukum / akta
    public function lhk_store($rkn_id,Request $req){
        $rekanan = Rekanan::findOrFail($rkn_id);
        $validator = Validator::make($req->all(),[
            'lhkp_notaris'=>'required',
            'lhkp_no'=>'required',
            'lhkp_tanggal'=>'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        
        $lhk = RknLndsnHukum::create([
            'rkn_id'=>$rekanan->rkn_id,
            'lhkp_notaris'=>$req->lhkp_notaris,
            'lhkp_tanggal'=>$req->lhkp_tanggal,
            'lhkp_no'=>$req->lhkp_no,
            'audituser'=>(isset($this->user))? $this->user['user']:'',
            'lhkp_id_attachment'=>$req->lhkp_id_attachment
        ]);
        //return response JSON user is created
        if($lhk) {
            return response()->json([
                'success' => true,
                'data'    => $lhk,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
        
    }
    
    public function lhk_update($lhk_id,Request $req){
        $validator = Validator::make($req->all(),[]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $akta = RknLndsnHukum::findOrFail($lhk_id);
        $data = $req->all();
        (isset($this->user))?$data['audituser']= $this->user['user']:'';
        $akta->update($data);
        if($akta) {
            return response()->json([
                'success' => true,
                'data'    => $akta,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
            'data'=>$data
        ], 201);
    }
    
    public function lhk($lhk_id){
        $akta = RknLndsnHukum::findOrFail($lhk_id);
        if($akta) {
            return response()->json([
                'success' => true,
                'data'    => $akta,  
            ], 201);
        }
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function lhk_list($rkn_id,Request $req){
        $akta = RknLndsnHukum::where('rkn_id',$rkn_id);
        $l =10;
        $total = $akta->count();
        $path = URL::current();
        if($req->length){
            $l = $req->length;
            $path.='?length='.$l;
        }        
        $list = $akta->paginate($l);
        if ($req->q) {
            $q = $req->q;
            $list = $akta->Where('lhkp_no','LIKE',"%{$q}%")
                    ->orWhere('lhkp_notaris','LIKE',"%{$q}%")->paginate($l);
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $list->withPath(url($path));
        return response()->json([
            'succes'=>true,
            'total'=>$total,
            'data'=>$list
        ],201);
    }
    
    
    // manajerial / pengurus perusahaan
    public function mnj_store($rkn_id,Request $req){
        $rekanan = Rekanan::findOrFail($rkn_id);
        $validator = Validator::make($req->all(),[
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $data = [
            'id_manajerial'=> $this->generateID('manajerial_rekanan', 'id_manajerial'),
            'rkn_id'=>$rekanan->rkn_id,
            'mjr_nama'=>$req->mjr_nama,
            'mjr_ktp'=>$req->mjr_ktp,
            'mjr_alamat'=>$req->mjr_alamat,
            'mjr_npwp'=>$req->mjr_npwp,
            'mjr_jenis'=>$req->mjr_jenis,
            'audituser'=>(isset($this->user))? $this->user['user']:'',
            'auditupdate'=>\Carbon\Carbon::now()
        ];
        $manajer = DB::table('manajerial_rekanan')->insert($data);
         //return response JSON user is created
        if($manajer) {
            return response()->json([
                'success' => true,
                'data'    => $data,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function mnj_update($id_mnj,Request $req){
        $validator = Validator::make($req->all(),[
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $data = $req->all();
        $data['auditupdate']= \Carbon\Carbon::now();
        (isset($this->user))?$data['audituser']= $this->user['user']:'';
        $manajer = DB::table('manajerial_rekanan')->where('id_manajerial',$id_mnj)->update($data);
        if($manajer) {
            return response()->json([
                'success' => true,
                'data'    => $data,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
            'data'=>$data
        ], 409);
    }
    
    public function mnj($id_mnj){
        $mnj = DB::table('manajerial_rekanan')->where('id_manajerial',$id_mnj)->first();
        if($mnj) {
            return response()->json([
                'success' => true,
                'data'    => $mnj,  
            ], 201);
        }
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function mnj_list($rkn_id,Request $req){
        $mnj = DB::table('manajerial_rekanan')->where('rkn_id',$rkn_id);
        $l =10;
        $total = $mnj->count();
        $path = URL::current();
        if($req->length){
            $l = $req->length;
            $path.='?length='.$l;
        }  
        $list = $mnj->paginate($l);
        if ($req->q) {
            $q = $req->q;
            $list = $mnj->Where('mjr_nama','LIKE',"%{$q}%")->paginate($l);
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $list->withPath(url($path));
        return response()->json([
            'succes'=>true,
            'total'=>$total,
            'data'=>$list
        ],201);
    }
    
    
    // pajak yang sudah terbayar
    public function pjk_store($rkn_id,Request $req){
        $rekanan = Rekanan::findOrFail($rkn_id);
        $validator = Validator::make($req->all(),[
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $pjk = RknPjk::create([
            'rkn_id'=>$rekanan->rkn_id,
            'pjk_jenis'=>$req->pjk_jenis,
            'pjk_no'=>$req->pjk_no,
            'pjk_tanggal'=>$req->pjk_tanggal,
            'pjk_tahun'=>$req->pjk_tahun,
            'audituser'=>(isset($this->user))? $this->user['user']:'',
            'pjk_id_attachment'=>$req->pjk_id_attachment
        ]);
        if($pjk) {
            return response()->json([
                'success' => true,
                'data'    => $pjk,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function pjk_update($pjk_id,Request $req){
        $validator = Validator::make($req->all(),[]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $pjk = RknPjk::findOrFail($pjk_id);
        $data = $req->all();
        (isset($this->user))?$data['audituser']= $this->user['user']:'';
        $pjk->update($data);
        if($pjk) {
            return response()->json([
                'success' => true,
                'data'    => $pjk,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
            'data'=>$data
        ], 201);
    }
    
    public function pjk($pjk_id){
        $pjk = RknPjk::findOrFail($pjk_id);
        if($pjk) {
            return response()->json([
                'success' => true,
                'data'    => $pjk,  
            ], 201);
        }
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function pjk_list($rkn_id,Request $req){
        $pjk = RknPjk::where('rkn_id',$rkn_id);
        $l =10;
        $total = $pjk->count();
        $path = URL::current();
        if($req->length){
            $l = $req->length;
            $path.='?length='.$l;
        }        
        $list = $pjk->paginate($l);
        if ($req->q) {
            $q = $req->q;
            $list = $pjk->Where('pjk_jenis','LIKE',"%{$q}%")
                    ->orWhere('pjk_no','LIKE',"%{$q}%")->paginate($l);
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $list->withPath(url($path));
        return response()->json([
            'succes'=>true,
            'total'=>$total,
            'data'=>$list
        ],201);
    }
    
    
    //pengalaman 
    public function pgl_store($rkn_id,Request $req){
        $rekanan = Rekanan::findOrFail($rkn_id);
        $validator = Validator::make($req->all(),[
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $pgl = RknPglaman::create([
            'rkn_id'=>$rekanan->rkn_id,
            'pgl_kegiatan'=>$req->pgl_kegiatan,
            'pgl_pembtgs'=>$req->pgl_pembtgs,
            'pgl_nokontrak'=>$req->pgl_nokontrak,
            'pgl_nilai'=>$req->pgl_nilai,
            'pgl_almtpembtgs'=>$req->pgl_almtpembtgs,
            'pgl_telppembtgs'=>$req->pgl_telppembtgs,
            'pgl_lokasi'=>$req->pgl_lokasi,
            'pgl_tglkontrak'=>$req->pgl_tglkontrak,
            'pgl_tglprogress'=>$req->pgl_tglprogress,
            'pgl_slskontrak'=>$req->pgl_slskontrak,
            'pgl_persenprogress'=>$req->pgl_persenprogress,
            'audituser'=>(isset($this->user))? $this->user['user']:'',
            'pgl_id_attachment'=>$req->pgl_id_attachment
        ]);
        if($pgl) {
            return response()->json([
                'success' => true,
                'data'    => $pgl,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function pgl_update($pen_id,Request $req){
        $validator = Validator::make($req->all(),[]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $pgl = RknPglaman::findOrFail($pen_id);
        $data = $req->all();
        $pgl->update($data);
        (isset($this->user))?$data['audituser']= $this->user['user']:'';
        if($pgl) {
            return response()->json([
                'success' => true,
                'data'    => $pgl,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
            'data'=>$data
        ], 201);
    }
    
    public function pgl($pen_id){
        $pgl = RknPglaman::findOrFail($pen_id);
        if($pgl) {
            return response()->json([
                'success' => true,
                'data'    => $pgl,
            ], 201);
        }
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function pgl_list($rkn_id,Request $req){
        $pgl = RknPglaman::where('rkn_id',$rkn_id);
        $l =10;
        $total = $pgl->count();
        $path = URL::current();
        if($req->length){
            $l = $req->length;
            $path.='?length='.$l;
        }        
        $list = $pgl->paginate($l);
        if ($req->q) {
            $q = $req->q;
            $list = $pgl->Where('pgl_kegiatan','LIKE',"%{$q}%")
                    ->orWhere('pgl_nokontrak','LIKE',"%{$q}%")
                    ->orWhere('pgl_pembtgs','LIKE',"%{$q}%")->paginate($l);
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $list->withPath(url($path));
        return response()->json([
            'succes'=>true,
            'total'=>$total,
            'data'=>$list
        ],201);
    }
    
    
    //peralatan 
    public function prl_store($rkn_id,Request $req){
        $rekanan = Rekanan::findOrFail($rkn_id);
        $validator = Validator::make($req->all(),[
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $data = [
            'id_prl'=> $this->generateID('peralatan_rekanan', 'id_prl'),
            'rkn_id'=>$rekanan->rkn_id,
            'alt_jenis'=>$req->alt_jenis,
            'alt_jumlah'=>$req->alt_jumlah,
            'alt_kapasitas'=>$req->alt_kapasitas,
            'alt_merktipe'=>$req->alt_merktipe,
            'alt_thpembuatan'=>$req->alt_thpembuatan,
            'alt_kondisi'=>$req->alt_kondisi,
            'alt_lokasi'=>$req->alt_lokasi,
            'alt_kepemilikan'=>$req->alt_kepemilikan,
            'audituser'=>(isset($this->user))? $this->user['user']:'',
            'auditupdate'=>\Carbon\Carbon::now()
        ];
        $prl = DB::table('peralatan_rekanan')->insert($data);
        if($prl) {
            return response()->json([
                'success' => true,
                'data'    => $data,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function prl_update($id_prl,Request $req){
        $validator = Validator::make($req->all(),[
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $data = $req->all();
        (isset($this->user))?$data['audituser']= $this->user['user']:'';
        $data['auditupdate']= \Carbon\Carbon::now();
        $prl = DB::table('peralatan_rekanan')->where('id_prl',$id_prl)->update($data);
        if($prl) {
            return response()->json([
                'success' => true,
                'data'    => $data,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
            'data'=>$data
        ], 409);
    }
    
    public function prl($id_prl){
        $prl = DB::table('peralatan_rekanan')->where('id_prl',$id_prl)->first();
        if($prl) {
            return response()->json([
                'success' => true,
                'data'    => $prl,  
            ], 201);
        }
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function prl_list($rkn_id,Request $req){
        $prl = DB::table('peralatan_rekanan')->where('rkn_id',$rkn_id);
        $l =10;
        $total = $prl->count();
        $path = URL::current();
        if($req->length){
            $l = $req->length;
            $path.='?length='.$l;
        }  
        $list = $prl->paginate($l);
        if ($req->q) {
            $q = $req->q;
            $list = $prl->Where('alt_jenis','LIKE',"%{$q}%")
                    ->orWhere('alt_merktipe','LIKE',"%{$q}%")->paginate($l);
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $list->withPath(url($path));
        return response()->json([
            'succes'=>true,
            'total'=>$total,
            'data'=>$list
        ],201);
    }
    
    
    //staf ahli perusahaan
    public function sta_store($rkn_id,Request $req){
        $rekanan = Rekanan::findOrFail($rkn_id);
         $validator = Validator::make($req->all(),[
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $sta = RknStaf::create([
            'rkn_id'=>$rekanan->rkn_id,
            'sta_jabatan'=>$req->sta_jabatan,
            'sta_keahlian'=>$req->sta_keahlian,
            'sta_nama'=>$req->sta_nama,
            'sta_kewarganegaraan'=>$req->sta_kewarganegaraan,
            'sta_pengalaman'=>$req->sta_pengalaman,
            'sta_telepon'=>$req->sta_telepon,
            'sta_pendidikan'=>$req->sta_pendidikan,
            'audituser'=>(isset($this->user))? $this->user['user']:'',
            'sta_alamat'=>$req->sta_alamat,
            'sta_npwp'=>$req->sta_npwp,
            'sta_jenis_kelamin'=>$req->sta_jenis_kelamin,
            'sta_email'=>$req->sta_email,
            'sta_tgllahir'=>$req->sta_tgllahir,
            'sta_status'=>$req->sta_status,
            'sta_id_attachment'=>$req->sta_id_attachment
        ]);
        if($sta) {
            return response()->json([
                'success' => true,
                'data'    => $sta  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
            'data'=>$req->all()
        ], 409);
    }
    
    public function sta_update($stp_id,Request $req){
        $validator = Validator::make($req->all(),[
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $sta = RknStaf::findOrFail($stp_id);
        $data = $req->all();
        (isset($this->user))?$data['audituser']= $this->user['user']:'';
        $sta->update($data);
        if($sta) {
            return response()->json([
                'success' => true,
                'data'    => $sta,  
            ], 201);
        }
        //return JSON process insert failed 
        return response()->json([
            'success' => false,
            'data'=>$data
        ], 409);
    }
    
    public function sta($stp_id){
        $sta = RknStaf::findOrFail($stp_id);
        if($sta) {
            return response()->json([
                'success' => true,
                'data'    => $sta,  
            ], 201);
        }
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function sta_list($rkn_id,Request $req){
        $sta = RknStaf::where('rkn_id',$rkn_id);
        $l =10;
        $total = $sta->count();
        $path = URL::current();
        $list = $sta->paginate($l);
        if ($req->q) {
            $q = $req->q;
            $list = $sta->Where('sta_nama','LIKE',"%{$q}%")
                    ->orWhere('sta_jabatan','LIKE',"%{$q}%")
                    ->orWhere('sta_keahlian','LIKE',"%{$q}%")->paginate($l);
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $list->withPath(url($path));
        return response()->json([
            'succes'=>true,
            'total'=>$total,
            'data'=>$list
        ],201);
    }
    
    
    
    
    private function generateID($table, $key) {
        do {
            $id = random_int("1" . str_repeat("0", 7), str_repeat("9", 8));
        }while(DB::table($table)->where($key,$id)->exists());
        return $id;
    }
}
