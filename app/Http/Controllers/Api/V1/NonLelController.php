<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\NonLelSeleksi;
use App\Http\Controllers\Api\AuthController;
use App\Models\DokPersiapan;
use App\Models\DokNonLel;
use App\Models\DokNonLelContent;
use App\Models\Persetujuan;
use App\Http\Controllers\Api\V1\LelUtilityController;

class NonLelController extends Controller
{
    //
    
    protected $user = null;
    protected $util ;


    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
        $this->util = new LelUtilityController();
    }
    
    public function initiate_lelang($pkt_id){
        $paket = Paket::where('pkt_id',$pkt_id)->with('panitia','pp')->first();
//        $data = [];
        (isset($this->user))?$data['audituser']= $this->user['user']:'';
        (isset($this->user))?$data['kipbj_id']= $this->user['user_id']:'';
        $data['pkt_tgl_assign'] = \Carbon\Carbon::now();
        $data['pkt_status'] = 1;
        $paket->update($data);
        $lel = $this->initiate_Non($paket);
        $dok = $this->initiate_Dok($lel);
        (count($paket->pp)>0)? $stj = $this->int_approvalpp($lel, $paket->pp[0]): $stj=null;
        (count($paket->panitia)>0)? $stj = $this->int_approvalpanitia($lel, $paket->panitia[0]): $stj=null;
        $val = ['nonLelang'=> $lel,'dokumen'=>$dok,'persetujuan'=>$stj];
        return response()->json(['success' => true,'data' => $val], 201);
//        return response()->json(['success' => true,'data' => $paket->pp[0]],201);
    }
    
    public function get_lelang($lls_id){
        $non = NonLelSeleksi::where('lls_id',$lls_id)->with('paket:pkt_id,lls_kontrak_pekerjaan')->first();
        $dok = DokNonLel::where('lls_id',$lls_id)->leftJoin('dok_nonlelang_content','dok_nonlelang_content.dll_id','=','dok_nonlelang.dll_id')->latest('dok_nonlelang_content.dll_versi')->select('dok_nonlelang.*','dok_nonlelang_content.dll_versi','dok_nonlelang_content.durasi','dok_nonlelang_content.dll_nomorsdp','dok_nonlelang_content.dll_tglsdp')->first();
//        $dok = DokNonLel::where('lls_id',$lls_id)->first();
        $kont = DokNonLelContent::where('dll_id',$dok->dll_id)->latest('dll_versi')->first();
        $jwl = $this->util->lelang_jadwal($non->lls_id);
        $chk = ['kualifikasi'=>$this->util->get_check_kual($dok->dll_id),'penawaran'=>$this->util->get_check_pen($dok->dll_id)];
        $stj = Persetujuan::where('lls_id',$non->lls_id)->with('pegawai:peg_id,peg_nama')->get();
        $stjp = Persetujuan::where('lls_id',$non->lls_id)->where('peg_id', $this->user['user_id'])->first();
        $stjp['list'] = $stj;
        $psr = $this->util->get_peserta($non->lls_id);
        $data = ['nonLel'=>$non,'dokumen'=>$dok,'content'=>$kont,'jadwal'=>$jwl,'checklist'=>$chk,'persetujuan'=>$stjp,'peserta'=>$psr];
        return response()->json(['success' => true,'data' => $data], 201);
    }
   
    public function get_lelang_penyedia($lls_id){
        $non = NonLelSeleksi::where('lls_id',$lls_id)->select('nonlelang_seleksi.*',DB::raw('tahap_nama_now( nonlelang_seleksi.lls_id, now( )::TIMESTAMP) as tahapan'))->first();
        $dok = DokNonLel::where('lls_id',$lls_id)->first();
        $chk = $this->util->get_check_kual($dok->dll_id);
        return response()->json(['success'=>true,'data'=>['lelang'=>$non,'kualifikasi'=>$chk]],201);
    }
    
    public function get_dokumen($lls_id,$mode = true){
        $data['lelang'] = NonLelSeleksi::where('lls_id',$lls_id)->select('nonlelang_seleksi.*',DB::raw('tahap_nama_now ( nonlelang_seleksi.lls_id, now( ) :: TIMESTAMP ) as tahapan'))->first();
        $data['dokumen'] = DokNonLel::where('lls_id',$data['lelang']->lls_id)->first();
        $data['konten'] = DokNonLelContent::where('dll_id',$data['dokumen']->dll_id)->latest('dll_versi')->first();
        $data['checklist'] = ['kualifikasi'=>$this->util->get_check_kual($data['dokumen']->dll_id,true),'penawaran'=>$this->util->get_check_pen($data['dokumen']->dll_id,true)];
        if($mode == true){return response()->json(['success'=>true,'data'=>$data],201);}else{ return $data;}
    }
    
    public function update_pp($lls_id,Request $req){
        $lel = NonLelSeleksi::findOrFail($lls_id);
        $data = [];
        if (!empty($req->nonLel)) {
            $data['nonLel'] = $this->update_lelang($lel->lls_id, $req->nonLel);
        }
        if(!empty($req->jadwal)){
            $data['jadwal'] = $this->mass_update_jadwal($req->jadwal);
        }
        if(!empty($req->dokumen)){
            $data['dokumen'] = $this->update_dok($req->dokumen);
        }
        if(!empty($req->persetujuan)){
            $data['persetujuan'] = $this->setuju($lel->lls_id,$req->persetujuan);
        }
        return response()->json(['success' => true,'data' =>$data], 201);
//        dd($data);
    }
    
    
    public function update_penyedia($lls_id){
        $lel = NonLelSeleksi::findOrFail($lls_id);
        $lel->update(['lls_status'=>1]);
        return $lel;
    }
    
    protected function mass_update_jadwal($jadwal){
        $data = [];
        foreach($jadwal as $jd){
            $jd['audituser'] = (isset($this->user))? $this->user['user']:null;
            array_push($data, $this->util->update_jadwal($jd['dtj_id'], $jd));
        }
        return $data;
    }

    
    protected function update_dok($dokumen){
        $dok = $this->util->update_dokumen($dokumen['dll_id'], ['dll_id_attachment'=>$dokumen['dll_id_attachment'],'audituser'=>(isset($this->user))? $this->user['user']:null]);
        $dokumen['dll_content_attachment'] = $dokumen['dll_id_attachment'];
        unset($dokumen['dll_id_attachment']);
        $dokumen['audituser'] = (isset($this->user))? $this->user['user']:null;
        $kont = $this->util->update_dok_content($dokumen['dll_id'], $dokumen['dll_versi'], $dokumen);
        return ['dokumen'=>$dok,'content'=>$kont];
    }
    
    
    //initiate lelang
    protected function initiate_Non($paket){
        return NonLelSeleksi::updateOrCreate(['pkt_id'=>$paket->pkt_id,'lls_versi_lelang'=>1],['pkt_id'=>$paket->pkt_id,'lls_status'=>0,'lls_versi_lelang'=>1,'pkt_hps'=>$paket->pkt_hps,'pkt_pagu'=>$paket->pkt_pagu,'pkt_nama'=>$paket->pkt_nama]);        
    }
    
    protected function initiate_Dok($non){
        $dok = DokPersiapan::where('pkt_id',$non->pkt_id)->first();
        $dokll = DokNonLel::create(['lls_id'=>$non->lls_id,'dll_nama_dokumen'=>'['.$non->lls_id.']'.$non->pkt_nama]);
        $content = DokNonLelContent::create(['dll_id'=>$dokll->dll_id,'dll_versi'=>1,'dll_lainnya'=>$dok->dp_lainnya,'dll_sskk'=>$dok->dp_sskk,'dll_spek'=>$dok->dp_spek,'dll_dkh'=>$dok->dp_dkh,'dll_sskk_attachment'=>$dok->dp_sskk_attachment]);
        return ['dokumen'=>$dokll,'content'=>$content];
    }
    
    
    //approval
    protected function setuju($lls_id,$setuju){
        if(isset($this->user)){
            $stj = Persetujuan::where([['peg_id', $this->user['user_id']],['lls_id',$lls_id]]);
            $setuju['audituser'] = $this->user['user'];
            $setuju['pst_tgl_setuju'] = ($setuju['pst_status'] == 1) ? \Carbon\Carbon::now() : null;
            $stj->update($setuju);
            return $stj->first();
        }
        return false;
    }
    
    
    protected function int_approvalpp($non,$pp){
//        $pp = DB::table('paket_pp')->where('pkt_id',$non->pkt_id)->first();
        Persetujuan::where('lls_id',$non->lls_id)->delete();
        return Persetujuan::create(['lls_id'=>$non->lls_id,'peg_id'=>$pp->peg_id]);
    }
    
    protected function int_approvalpanitia($non,$panitia){
        $anggt = DB::table('anggota_panitia')->where('pnt_id',$panitia->pnt_id)->get();
        $stj = [];
        foreach($anggt as $ag){
            array_push($stj,Persetujuan::create(['lls_id'=>$non->lls_id,'peg_id'=>$ag->peg_id]));
        }
        return $stj;
    }



    protected function update_lelang($lls_id,$data){
        $lel = NonLelSeleksi::findOrFail($lls_id);
        if(isset($this->user)){$data['audituser'] = $this->user['user'];} 
        $lel->update($data);
        return $lel;
    }
    
    //peserta
    public function list_paketInbound(Request $req,$user_id){
        $non = NonLelSeleksi::Peserta($user_id)->select('nonlelang_seleksi.*',DB::raw('tahap_nama_now( nonlelang_seleksi.lls_id, now( )::TIMESTAMP) as tahapan'))->where('lls_status',0)->where(DB::raw('aproval(nonlelang_seleksi.lls_id)'),'SETUJU');
        return $this->paginate($req, $non);
    }
    
    public function list_lelang(Request $req,$user_id){
        $non = NonLelSeleksi::Peserta($user_id)->select('nonlelang_seleksi.*',DB::raw('tahap_nama_now( nonlelang_seleksi.lls_id, now( )::TIMESTAMP) as tahapan'))->where('lls_status',1);
        return $this->paginate($req, $non);
    }

    //pejabat pengadaan
    public function list_paketDown(Request $req,$user_id) {
        $non = NonLelSeleksi::PP($user_id)->select('nonlelang_seleksi.*',DB::raw('tahap_now ( nonlelang_seleksi.lls_id, now( ) :: TIMESTAMP ) as tahapan'))->with('paket');
        return $this->paginate($req, $non);
    }
    
    public function list_paketUp(Request $req,$user_id) {
        $non = NonLelSeleksi::Panitia($user_id)->select('nonlelang_seleksi.*',DB::raw('tahap_now ( nonlelang_seleksi.lls_id, now( ) :: TIMESTAMP ) as tahapan'))->with('paket');
        return $this->paginate($req, $non);
    }
    
     public function list_lelangDown(Request $req,$user_id) {
        $non = NonLelSeleksi::PP($user_id)->select('nonlelang_seleksi.*',DB::raw('tahap_nama_now ( nonlelang_seleksi.lls_id, now( ) :: TIMESTAMP ) as tahapan'))->where('lls_status',1);
        return $this->paginate($req, $non);
    }
    
    public function list_lelangUp(Request $req,$user_id) {
        $non = NonLelSeleksi::Panitia($user_id)->select('nonlelang_seleksi.*',DB::raw('tahap_nama_now ( nonlelang_seleksi.lls_id, now( ) :: TIMESTAMP ) as tahapan'))->where('lls_status',1);
        return $this->paginate($req, $non);
    }
    
    public function list_lelangKIPBJ(Request $req,$user_id){
        $non = NonLelSeleksi::KIPBJ($user_id)->select('nonlelang_seleksi.*',DB::raw('tahap_nama_now ( nonlelang_seleksi.lls_id, now( ) :: TIMESTAMP ) as tahapan'))->where('lls_status',1);
        return $this->paginate($req, $non);
    }
    
    public function list_lelangPPK(Request $req,$user_id){
        $non = NonLelSeleksi::PPK($user_id)->select('nonlelang_seleksi.*',DB::raw('tahap_nama_now ( nonlelang_seleksi.lls_id, now( ) :: TIMESTAMP ) as tahapan'))->where('lls_status',1);
        return $this->paginate($req, $non);
    }
    
    protected function paginate($req,$non){
        $l = 10;
        $path = URL::current();
        $total = $non->count();
        if ($req->length) {
            $l = $req->length;
            $path .= '?length=' . $l;
        }
        if($req->q){
            $q = $req->q;
            $list = $non->orWhere('nonlelang_seleksi.pkt_nama','LIKE',"%{$q}%")
            ->orWhere('nonlelang_seleksi.lls_id','LIKE',"%{$q}%")->paginate($l);
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $list = $non->paginate($l);
        $list->withPath(url($path));
        return response()->json([
                    'success' => true,
                    'total' => $total,
                    'data' => $list
                        ], 201);
    }
}
