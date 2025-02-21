<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\NonLelSeleksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Models\Jadwal;
use App\Models\Checklist;
use App\Models\DokNonLel;
use App\Models\DokNonLelContent;
use App\Models\Rekanan;
use App\Models\Peserta;
use Illuminate\Support\Facades\Http;

class LelUtilityController extends Controller {

    protected $user = null;
    
    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
    }
    
    public function template_jadwal() {
        $data = DB::table('aktivitas_pl')->whereIn('akt_id', [1, 2, 3, 4, 5])->get();
        return response()->json(['success' => true, 'data' => $data], 201);
    }
    
    public function lelang_jadwal($lls_id){
        $lel = NonLelSeleksi::findOrFail($lls_id);
        if (count($this->get_jadwal($lel->lls_id))==0) {
            $base = DB::table('aktivitas_pl')->whereIn('akt_id', [1, 2, 3, 4, 5])->get();
            foreach ($base as $jdw) {
                Jadwal::create(['lls_id' => $lel->lls_id, 'akt_id' => $jdw->akt_id]);
            }
        }
        $jadwal = $this->get_jadwal($lel->lls_id);
//        return response()->json(['success' => true,'data' => $jadwal], 201);
        return $jadwal;
    }
    
    public function update_jadwal($dtj_id,$data){
        $jdwl = Jadwal::findOrFail($dtj_id);
        $jdwl->update($data);
        return $jdwl;
    }
    
    public function update_dokumen($dll_id,$data){
        $dok = DokNonLel::findOrFail($dll_id);
        $dok->update($data);
        return $dok;
    }
    
    public function update_dok_content($dll_id,$versi,$data){
        $cont = DokNonLelContent::where('dll_id',$dll_id)->where('dll_versi',$versi);
        $cont->update($data);
        return $cont->first();
    }
    
    public function update_chk_kual($dll_id,Request $req){
        $dok = DokNonLel::findOrFail($dll_id);
        $old = $this->get_check_kual($dok->dll_id);
        if (count($old) > 0) {
            foreach ($old as $ol) {
                $this->chk_prf_delete($ol->chk_id);
            }
        }
        $data = [];
        if (isset($req->checklist)) {
            foreach ($req->checklist as $nw) {
                $hasil = $this->chk_prf_insert(['dll_id' => $dok->dll_id, 'ckm_id' => $nw['ckm_id']]);
                array_push($data, $hasil);
            }
        }
        return response()->json(['success' => true, 'data' => $data], 201);
    }
    
    public function update_chk_pen($dll_id,Request $req){
        $dok = DokNonLel::findOrFail($dll_id);
        $old = $this->get_check_pen($dok->dll_id);
        if (count($old) > 0) {
            foreach ($old as $ol) {
                $this->chk_prf_delete($ol->chk_id);
            }
        }
        $data = [];
        if (isset($req->checklist)) {
            foreach ($req->checklist as $nw) {
                $hasil = $this->chk_prf_insert(['dll_id' => $dok->dll_id, 'ckm_id' => $nw['ckm_id']]);
                array_push($data, $hasil);
            }
        }
        return response()->json(['success' => true, 'data' => $data], 201);
    }
        
    public function check_kual($lls_id){
        $lel = NonLelSeleksi::findOrFail($lls_id);
        $data['administrasi'] = $this->kual_adm($lel);
        $data['teknis'] = $this->kual_gnrl($lel,6);
        $data['keuangan'] = $this->kual_gnrl($lel,7);
        return response()->json(['success' => true, 'data' => $data], 201);
    }
    
    public function check_pen($lls_id){
        $lel = NonLelSeleksi::findOrFail($lls_id);
        $data['administrasi'] = $this->pen_gnrl($lel,1);
        $data['teknis'] = $this->pen_gnrl($lel,2);
        $data['harga'] = $this->pen_gnrl($lel,3);
        return response()->json(['success' => true, 'data' => $data], 201);
    }
    
    public function get_check_kual($dll_id,$break = false){
//        $chk = Checklist::where('dll_id',$dll_id)->leftJoin('checklist_master','checklist_master.ckm_id','=','checklist.ckm_id')->orderBy('ckm_urutan', 'asc');
//        $kual = $this->base_chk_by(['dll_id'=>$dll_id])->whereIn('checklist_master.ckm_jenis',[5,6,7])->get();
        if($break){
            $kual['administrasi'] = $this->base_chk_by(['dll_id'=>$dll_id])->where('checklist_master.ckm_jenis',5)->get();
            $kual['teknis'] = $this->base_chk_by(['dll_id'=>$dll_id])->where('checklist_master.ckm_jenis',6)->get();
            $kual['keuangan'] = $this->base_chk_by(['dll_id'=>$dll_id])->where('checklist_master.ckm_jenis',7)->get();
        }else{
            $kual = $this->base_chk_by(['dll_id'=>$dll_id])->whereIn('checklist_master.ckm_jenis',[5,6,7])->get();
        }        
        return $kual;
    }
    
    public function get_check_pen($dll_id, $break = false){
//        $chk = Checklist::where('dll_id',$dll_id)->leftJoin('checklist_master','checklist_master.ckm_id','=','checklist.ckm_id')->orderBy('ckm_urutan', 'asc');
//        $pen = $this->base_chk_by(['dll_id'=>$dll_id])->whereIn('checklist_master.ckm_jenis',[1,2,3])->get();
        if($break){
            $pen['administrasi'] = $this->base_chk_by(['dll_id'=>$dll_id])->where('checklist_master.ckm_jenis',1)->get();
            $pen['teknis'] = $this->base_chk_by(['dll_id'=>$dll_id])->where('checklist_master.ckm_jenis',2)->get();
            $pen['harga'] = $this->base_chk_by(['dll_id'=>$dll_id])->where('checklist_master.ckm_jenis',3)->get();
        }else{
            $pen = $this->base_chk_by(['dll_id'=>$dll_id])->whereIn('checklist_master.ckm_jenis',[1,2,3])->get();
        }
        
        return $pen;
    }
    
    public function tambah_peserta($lls_id,Request $req){
        $non = NonLelSeleksi::findOrFail($lls_id);
        $rkn = Rekanan::findOrFail($req->rkn_id);
        $bl = $this->blacklist_checker($rkn->rkn_npwp);
        $psrdt = ['rkn_id'=>$rkn->rkn_id,'lls_id'=>$non->lls_id];
        $psrdt['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN';
        $blc = ['lls_id'=>$non->lls_id,'rkn_id'=>$rkn->rkn_id] ;
        if($bl['result']->status){
            $blc['bch_status'] = 1;
            $blc['bch_url'] = $bl['result']->detail_url;
            $this->blacklist_insert($blc);
            $psrdt['psr_black_list'] = 1;
            $psr = Peserta::updateOrCreate(['lls_id'=>$non->lls_id],$psrdt);
            return response()->json(['success' => true, 'data' => ['message'=>'Penyedia Dengan NPWP '.$rkn->rkn_npwp.' Terdaftar Pada Daftar Hitam pada link berikut :','url'=>$bl['result']->detail_url,'peserta'=>$psr]], 201);
        }
        $blc['bch_status'] = 0;
        $blc['bch_url'] = $bl['url'];
        $this->blacklist_insert($blc);
        $psrdt['psr_black_list'] = 0;        
        $psr = Peserta::updateOrCreate(['lls_id'=>$non->lls_id],$psrdt);
        return response()->json(['success' => true, 'data' => $psr], 201);
    }
    
    public function get_peserta($lls_id){
        $rkn = Rekanan::join('peserta_nonlelang','rekanan.rkn_id', '=', 'peserta_nonlelang.rkn_id')->select('rekanan.*','peserta_nonlelang.psr_black_list')->where('peserta_nonlelang.lls_id',$lls_id)->first();
        return $rkn;
    }
    
    public function get_penyedia($lls_id,Request $req) {
        $lel = NonLelSeleksi::findOrFail($lls_id);
        $rekanan = new Rekanan();
        $l = 10;
        $total = $rekanan->count();
        $path = URL::current();
        if ($req->length) {
            $l = $req->length;
            $path .= '?length=' . $l;
        }
        $list = $rekanan->with('B_Usaha:btu_id,btu_nama')->paginate($l);
        if ($req->q) {
            $q = $req->q;
            $list = $rekanan->with('B_Usaha:btu_id,btu_nama')->where('rkn_nama', 'LIKE', "%{$q}%")
                            ->orWhere('rkn_npwp', 'LIKE', "%{$q}%")
                            ->orWhere('rkn_namauser', 'LIKE', "%{$q}%")->paginate($l);
            ($req->length) ? $path .= '&q=' . $q : $path .= '?q=' . $q;
        }
        $list->withPath(url($path));
        if (isset($lel)) {
            return response()->json(['success' => true,'total' => $total,'data' => $list], 201);
        }else{
            return response()->json(['success'=>false],401);
        }
    }
    
    protected function chk_prf_delete($chk_id){
        $chk = Checklist::findOrFail($chk_id);
        return $chk->delete();
    }
    
    protected function chk_prf_insert($data){
        $mtr = $this->chk_master_by($data['ckm_id']);
        $data['chk_nama'] = $mtr->ckm_nama;
        $chk = Checklist::create($data);
        return $chk;
    }
    
    
    protected function blacklist_insert($data){
        $data['bch_id'] = $this->generateID('blacklist_checker_history', 'bch_id');
        $data['peg_id'] = (isset($this->user))?$this->user['user_id']:null;
        $data['auditupdate'] = \Carbon\Carbon::now();
        $data['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN';
        return DB::table('blacklist_checker_history')->updateOrInsert(['lls_id'=>$data['lls_id']],$data);
    }

    protected function kual_adm($lel){
        if(in_array($lel->kgr_id, [0,1,3])){
            $asli = $this->check_master()->where('kgr_id', $lel->kgr_id)->where('ckm_jenis', 5)->where('ckm_status', 1)->orderBy('ckm_urutan', 'asc')->get();
            $tamb = $this->check_master()->where('ckm_id', 50)->get();
            return collect($tamb)->merge($asli);
        }else if(in_array($lel->kgr_id, [2,7,5,6])){
            return $this->check_master()->where('kgr_id',$lel->kgr_id)->where('ckm_jenis',5)->where('ckm_status',1)->where('ckm_versi',4)->orderBy('ckm_urutan','asc')->get();
        }else{
            return $this->check_master()->where('kgr_id',$lel->kgr_id)->where('ckm_jenis',5)->where('ckm_status',1)->orderBy('ckm_urutan','asc')->get();
        }
    }
    
    protected function kual_gnrl($lel,$jns) {
        if(in_array($lel->kgr_id, [0,1,3])){
            return $this->check_master()->where('kgr_id', $lel->kgr_id)->where('ckm_jenis', $jns)->where('ckm_status', 1)->where('ckm_checked',0)->orderBy('ckm_urutan', 'asc')->get();
        }else if(in_array($lel->kgr_id, [2,7,5,6])){
            return $this->check_master()->where('kgr_id',$lel->kgr_id)->where('ckm_jenis',$jns)->where('ckm_status',1)->where('ckm_versi',4)->orderBy('ckm_urutan','asc')->get();
        }else{
            return $this->check_master()->where('kgr_id',$lel->kgr_id)->where('ckm_jenis',$jns)->where('ckm_status',1)->orderBy('ckm_urutan','asc')->get();
        }
    }
    
    protected function pen_gnrl($lel,$jns){
        if($lel->kgr_id==1 && $jns==1){
            return $this->check_master()->whereNull('kgr_id')->where('ckm_jenis', $jns)->where('ckm_status', 1)->where('ckm_checked',1)->where('ckm_required',1)->orderBy('ckm_urutan', 'asc')->get();
        } else if($lel->kgr_id==1 && $jns==3){
            return $this->check_master()->whereNull('kgr_id')->where('ckm_jenis', $jns)->where('ckm_status', 1)->orderBy('ckm_urutan', 'asc')->get();
        }
        else if (in_array($lel->kgr_id, [0, 3]) && in_array($jns, [2,3])) {
            return $this->check_master()->where('kgr_id', $lel->kgr_id)->where('ckm_jenis', $jns)->where('ckm_status', 1)->where('ckm_checked',0)->orderBy('ckm_urutan', 'asc')->get();
        }else if (in_array($lel->kgr_id, [0,3])) {
            return $this->check_master()->where('kgr_id', $lel->kgr_id)->where('ckm_jenis', $jns)->where('ckm_status', 1)->where('ckm_checked', 1)->orderBy('ckm_urutan', 'asc')->get();
        } else if (in_array($lel->kgr_id, [2, 7, 5, 6])) {
            return $this->check_master()->where('kgr_id', $lel->kgr_id)->where('ckm_jenis', $jns)->where('ckm_status', 1)->where('ckm_versi', 4)->orderBy('ckm_urutan', 'asc')->get();
        } else {
            return $this->check_master()->where('kgr_id', $lel->kgr_id)->where('ckm_jenis', $jns)->where('ckm_status', 1)->orderBy('ckm_urutan', 'asc')->get();
        }
    }

    protected function base_chk_by($data){
        return Checklist::where($data)->leftJoin('checklist_master','checklist_master.ckm_id','=','checklist.ckm_id')->orderBy('ckm_urutan', 'asc');
    }


    protected function chk_master_by($ckm_id){
        return $this->check_master()->where('ckm_id',$ckm_id)->first();
    }


    protected function get_jadwal($lls_id){
        return Jadwal::where('lls_id',$lls_id)->leftJoin('aktivitas_pl','aktivitas_pl.akt_id','=','jadwal.akt_id')->select('jadwal.*','aktivitas_pl.akt_jenis','aktivitas_pl.nama')->get();
    }
    

    protected function check_master(){
        return DB::table('checklist_master');
    }
    
    public function coba_blacklist(Request $req){
        $npwp = $req->npwp;
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 12; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.58 Mobile Safari/537.36'
            ])->get("https://inaproc.id/api/blacklist/check/npwp?arg=".$npwp);
        dd($response->json());
    }
    
    protected function blacklist_checker($npwp){
//        $ch = curl_init(); 
        $url = "https://inaproc.id/api/blacklist/check/npwp?arg=".$npwp;
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        $output = curl_exec($ch);
//        curl_close($ch);
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 12; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.58 Mobile Safari/537.36'
            ])->get($url);
        return ['url'=>$url,'result'=>(object)$response->json()];
    }
    
    private function generateID($table, $key) {
        do {
            $id = random_int("1" . str_repeat("0", 7), str_repeat("9", 8));
        } while (DB::table($table)->where($key, $id)->exists());
        return $id;
    }
}
