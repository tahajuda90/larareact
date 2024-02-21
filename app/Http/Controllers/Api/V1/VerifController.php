<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Peserta;
use App\Models\Pegawai;
use App\Http\Controllers\Api\AuthController;

class VerifController extends Controller
{
    //
    protected $user = null;
    
    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
    }
    
    public function VerifData($psr_id){
        $psr = Peserta::where('psr_id',$psr_id)->first();
        $data['ijinusaha'] = $this->getVerifIjin(['lls_id'=>$psr->lls_id])->leftJoin('ijin_usaha_rekanan','ijin_usaha_rekanan.ius_id','=','verif_ijin_usaha.ius_id')->where(['ijin_usaha_rekanan.rkn_id'=>$psr->rkn_id])->select('ijin_usaha_rekanan.*','verif_ijin_usaha.verif_ius','verif_ijin_usaha.is_verif')->get();
        $data['manajerial'] = $this->getVerifManajerial(['lls_id'=>$psr->lls_id])->leftJoin('manajerial_rekanan','manajerial_rekanan.id_manajerial','=','verif_manajerial.id_manajerial')->where(['manajerial_rekanan.rkn_id'=>$psr->rkn_id])->select('manajerial_rekanan.*','verif_manajerial.verif_mnj','verif_manajerial.is_verif')->get();
        $data['landasanhkm'] = $this->getVerifLhk(['lls_id'=>$psr->lls_id])->leftJoin('landasan_hukum_rekanan','landasan_hukum_rekanan.lhkp_id','=','verif_lhk.lhkp_id')->where(['landasan_hukum_rekanan.rkn_id'=>$psr->rkn_id])->select('landasan_hukum_rekanan.*','verif_lhk.verif_lhkp','verif_lhk.is_verif')->get();
        $data['pengalaman'] = $this->getVerifPengalaman(['lls_id'=>$psr->lls_id])->leftJoin('pengalaman_rekanan','pengalaman_rekanan.pen_id','=','verif_pengalaman.pen_id')->where(['pengalaman_rekanan.rkn_id'=>$psr->rkn_id])->select('pengalaman_rekanan.*','verif_pengalaman.verif_pen','verif_pengalaman.is_verif')->get();
        $data['pajak'] = $this->getVerifPajak(['lls_id'=>$psr->lls_id])->leftJoin('pajak','pajak.pjk_id','=','verif_pajak.pjk_id')->where(['pajak.rkn_id'=>$psr->rkn_id])->select('pajak.*','verif_pajak.verif_pjk','verif_pajak.is_verif')->get();
        $data['peralatan'] = $this->getVerifPeralatan(['lls_id'=>$psr->lls_id])->leftJoin('peralatan_rekanan','peralatan_rekanan.id_prl','=','verif_peralatan.id_prl')->where(['peralatan_rekanan.rkn_id'=>$psr->rkn_id])->select('peralatan_rekanan.*','verif_peralatan.verif_prl','verif_peralatan.is_verif')->get();
        $data['stafahli'] = $this->getVerifStaf(['lls_id'=>$psr->lls_id])->leftJoin('staf_ahli_rekanan','staf_ahli_rekanan.stp_id','=','verif_stafahli.stp_id')->where(['staf_ahli_rekanan.rkn_id'=>$psr->rkn_id])->select('staf_ahli_rekanan.*','verif_stafahli.verif_stp','verif_stafahli.is_verif')->get();
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function DoVerifikasi($peg_id,Request $req){
        $peg = Pegawai::where(['peg_id'=>$peg_id])->first();
        $tbl = ['verif_ius'=> $this->getVerifIjin($req->all()),'verif_lhkp'=> $this->getVerifLhk($req->all()),'verif_mnj'=> $this->getVerifManajerial($req->all()),'verif_pjk'=> $this->getVerifPajak($req->all()),'verif_stp'=> $this->getVerifStaf($req->all()),'verif_pen'=> $this->getVerifPengalaman($req->all()),'verif_prl'=> $this->getVerifPeralatan($req->all())];
        if (array_key_exists(key($req->all()), $tbl)) {
            $tbl[key($req->all())]->update(['is_verif' => 1, 'peg_id' => $peg->peg_id]);
            $data = $tbl[key($req->all())]->first();
        } else {
            $data = 'data tidak ditemukan';
        }
        return response()->json(['success'=>true,'data'=> $data],201);
    }
    
    //get
    public function getVerifIjin($data){
        $tabel = 'verif_ijin_usaha';
        return DB::table($tabel)->where($data);        
    }
    
    public function getVerifLhk($data){
        $tabel = 'verif_lhk';
        return DB::table($tabel)->where($data);   
    }

    
    public function getVerifManajerial($data){
        $tabel = 'verif_manajerial';
        return DB::table($tabel)->where($data);      
    }
    
    public function getVerifPajak($data){
        $tabel = 'verif_pajak';
        return DB::table($tabel)->where($data);     
    }
    
    public function getVerifPengalaman($data){
        $tabel = 'verif_pengalaman';
        return DB::table($tabel)->where($data);        
    }
    
    public function getVerifPeralatan($data){
        $tabel = 'verif_peralatan';
        return DB::table($tabel)->where($data); 
    }
    
    public function getVerifStaf($data){
        $tabel = 'verif_stafahli';
        return DB::table($tabel)->where($data);     
    }
    
    
    //create
    public function createVerifIjin($data,$lls_id=null){
        $tabel = 'verif_ijin_usaha';
        if(isset($lls_id)){
            $ins = false;
            if (count($data) > 0) {
                $data = $this->bulk_creator($tabel, 'vid_ius', $lls_id, $data);
                $ins = DB::table($tabel)->insert($data);
            }
            return (object) ['success'=>$ins,'data'=>$data];
        }
        $data['vid_ius'] = $this->generateID($tabel, 'vid_ius');
        $ins = DB::table($tabel)->updateOrInsert($this->baseData($data));
        return (object) ['success'=>$ins,'data'=>$data];        
    }
    
    public function createVerifLhk($data,$lls_id=null){
        $tabel = 'verif_lhk';
        if (isset($lls_id)) {
            $ins = false;
            if (count($data) > 0) {
                $data = $this->bulk_creator($tabel, 'ver_lhkp_id', $lls_id, $data);
                $ins = DB::table($tabel)->insert($data);
            }
            return (object) ['success' => $ins, 'data' => $data];
        }
        $data['ver_lhkp_id'] = $this->generateID($tabel, 'ver_lhkp_id');
        $ins = DB::table($tabel)->updateOrInsert($this->baseData($data));
        return (object) ['success'=>$ins,'data'=>$data];        
    }

    
    public function createVerifManajerial($data,$lls_id=null){
        $tabel = 'verif_manajerial';
        if (isset($lls_id)) {
            $ins = false;
            if (count($data) > 0) {
                $data = $this->bulk_creator($tabel, 'verif_mnj', $lls_id, $data);
                $ins = DB::table($tabel)->insert($data);
            }
            return (object) ['success' => $ins, 'data' => $data];
        }
        $data['verif_mnj'] = $this->generateID($tabel, 'verif_mnj');
        $ins = DB::table($tabel)->updateOrInsert($this->baseData($data));
        return (object) ['success'=>$ins,'data'=>$data];        
    }
    
    public function createVerifPajak($data,$lls_id=null){
        $tabel = 'verif_pajak';
        if (isset($lls_id)) {
            $ins = false;
            if (count($data) > 0) {
                $data = $this->bulk_creator($tabel, 'verif_pjk', $lls_id, $data);
                $ins = DB::table($tabel)->insert($data);
            }
            return (object) ['success' => $ins, 'data' => $data];
        }
        $data['verif_pjk'] = $this->generateID($tabel, 'verif_pjk');
        $ins = DB::table($tabel)->updateOrInsert($this->baseData($data));
        return (object) ['success'=>$ins,'data'=>$data];        
    }
    
    public function createVerifPengalaman($data,$lls_id=null){
        $tabel = 'verif_pengalaman';
        if (isset($lls_id)) {
            $ins = false;
            if (count($data) > 0) {
                $data = $this->bulk_creator($tabel, 'verif_pen', $lls_id, $data);
                $ins = DB::table($tabel)->insert($data);
            }
            return (object) ['success' => $ins, 'data' => $data];
        }
        $data['verif_pen'] = $this->generateID($tabel, 'verif_pen');
        $ins = DB::table($tabel)->updateOrInsert($this->baseData($data));
        return (object) ['success'=>$ins,'data'=>$data];        
    }
    
    public function createVerifPeralatan($data,$lls_id=null){
        $tabel = 'verif_peralatan';
        if (isset($lls_id)) {
            $ins = false;
            if (count($data) > 0) {
                $data = $this->bulk_creator($tabel, 'verif_prl', $lls_id, $data);
                $ins = DB::table($tabel)->insert($data);
            }
            return (object) ['success' => $ins, 'data' => $data];
        }
        $data['verif_prl'] = $this->generateID($tabel, 'verif_prl');
        $ins = DB::table($tabel)->updateOrInsert($this->baseData($data));
        return (object) ['success'=>$ins,'data'=>$data];        
    }
    
    public function createVerifStaf($data,$lls_id=null){
        $tabel = 'verif_stafahli';
        if (isset($lls_id)) {
            $ins = false;
            if (count($data) > 0) {
                $data = $this->bulk_creator($tabel, 'verif_stp', $lls_id, $data);
                $ins = DB::table($tabel)->insert($data);
            }
            return (object) ['success' => $ins, 'data' => $data];
        }
        $data['verif_stp'] = $this->generateID($tabel, 'verif_stp');
        $ins = DB::table($tabel)->updateOrInsert($this->baseData($data));
        return (object) ['success'=>$ins,'data'=>$data];        
    }
    
    private function baseData($data){
        $data['auditupdate'] = \Carbon\Carbon::now();
        $data['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN';
        return $data;
    }
    
    private function bulk_creator($table,$key,$forkey,$bulk){
        $data = [];
        foreach($bulk as $bl){
            $bl[$key] = $this->generateID($table, $key);
            array_push($data, $this->baseData(array_merge($bl,$forkey)));
        }
        return $data;
    }

    private function generateID($table, $key) {
        do {
            $id = random_int("1" . str_repeat("0", 7), str_repeat("9", 8));
        }while(DB::table($table)->where($key,$id)->exists());
        return $id;
    }
}
