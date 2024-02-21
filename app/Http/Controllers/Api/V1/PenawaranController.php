<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
//use App\Models\DokPersiapan;
//use App\Exports\PersiapanExport;
use App\Exports\DkhExport;
use App\Models\Peserta;
use App\Models\DokPenawaran;
use App\Models\DokNonLel;
use App\Models\NonLelSeleksi;
use App\Models\DokNonLelContent;
use App\Models\Checklist;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\NonLelController;
use App\Http\Controllers\Api\V1\VerifController;

class PenawaranController extends Controller
{
    //
    
    protected $user = null;
    protected $lel ;
    protected $verif;


    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
        $this->verif = new VerifController();
    }
    
    public function get_gnrl_penawaran($lls_id){        
        $data['lelang'] = NonLelSeleksi::where('lls_id',$lls_id)->select('nonlelang_seleksi.*',DB::raw('tahap_nama_now ( nonlelang_seleksi.lls_id, now( ) :: TIMESTAMP ) as tahapan'),DB::raw('tahap_now ( nonlelang_seleksi.lls_id, now( ) :: TIMESTAMP ) as tahapan2'))->first();
        $data['dokumen'] = DokNonLel::where('lls_id',$lls_id)->first();
        $psr = Peserta::where('lls_id',$data['lelang']->lls_id)->where('rkn_id',$this->user['user_id'])->first();
        $data['kualifikasi'] = DokPenawaran::where('psr_id',$psr->psr_id)->where('dok_jenis',0)->first();
        $penawaran['surat'] = (isset($psr->tgl_surat_penawaran)) ?['tgl_surat_penawaran'=>$psr->tgl_surat_penawaran,'masa_berlaku_penawaran'=>$psr->masa_berlaku_penawaran] : null;
        $penawaran['teknis'] = DokPenawaran::where('psr_id',$psr->psr_id)->where('dok_jenis',1)->first(); 
        $penawaran['harga'] = DokPenawaran::where('psr_id',$psr->psr_id)->where('dok_jenis',2)->first();
        $penawaran['Total'] = $psr->psr_harga;
        $data['penawaran'] = $penawaran;
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    
    public function get_kualifikasi($lls_id){        
        $data['lelang'] = NonLelSeleksi::where('lls_id',$lls_id)->first();
        $psr = Peserta::where('rkn_id',$this->user['user_id'])->where('lls_id',$data['lelang']->lls_id)->first();
        $data['izinusaha'] = $this->verif->getVerifIjin(['lls_id'=>$lls_id])->get();
        $data['landasanhukum'] = $this->verif->getVerifLhk(['lls_id'=>$lls_id])->get();
        $data['manajerial'] = $this->verif->getVerifManajerial(['lls_id'=>$lls_id])->get();
        $data['pajak'] = $this->verif->getVerifPajak(['lls_id'=>$lls_id])->get();
        $data['stafahli'] = $this->verif->getVerifStaf(['lls_id'=>$lls_id])->get();
        $data['pengalaman'] = $this->verif->getVerifPengalaman(['lls_id'=>$lls_id])->get();
        $data['peralatan'] = $this->verif->getVerifPeralatan(['lls_id'=>$lls_id])->get();
        $data['lainya'] = DokPenawaran::where([['psr_id', '=', $psr->psr_id],['dok_jenis', '=', 6]])->first();
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function get_penawaran($lls_id){
        $dll = DokNonLel::where('lls_id',$lls_id)->first();
        $data['dok_lelang'] = DokNonLelContent::where('dll_id',$dll->dll_id)->select('durasi','dll_nomorsdp','dll_tglsdp')->latest('dll_versi')->first();
        $data['peserta'] = Peserta::select('peserta_nonlelang.*','rekanan.rkn_nama','nonlelang_seleksi.pkt_nama')->where([['peserta_nonlelang.rkn_id','=', $this->user['user_id']],['peserta_nonlelang.lls_id','=',$lls_id]])->leftJoin('rekanan','rekanan.rkn_id','=','peserta_nonlelang.rkn_id')->leftJoin('nonlelang_seleksi','nonlelang_seleksi.lls_id','=','peserta_nonlelang.lls_id')->first();
        $chk['teknis'] = $this->check_pnwrn($dll)->where('ckm_jenis', 2)->get();
        $chk['harga'] = $this->check_pnwrn($dll)->where('ckm_jenis', 3)->get();
        $data['checklist'] = $chk;
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function ins_kualifikasi($lls_id,Request $req){
        $lls = NonLelSeleksi::where('lls_id',$lls_id)->first();
        $psr = Peserta::where([['lls_id',$lls->lls_id],['rkn_id',$this->user['user_id']]])->first();
        $data= [];
        if(isset($req->izinusaha)){
            $this->verif->getVerifIjin(['lls_id'=>$lls_id])->delete();
            $hsl = $this->verif->createVerifIjin($req->izinusaha,['lls_id'=>$lls->lls_id]);
            $data['izinusaha'] = $hsl->data;
        }        
        if(isset($req->landasanhukum)){
            $this->verif->getVerifLhk(['lls_id'=>$lls_id])->delete();
            $hsl = $this->verif->createVerifLhk($req->landasanhukum,['lls_id'=>$lls->lls_id]);
            $data['landasanhukum'] = $hsl->data;
        }
        if(isset($req->manajerial)){
            $this->verif->getVerifManajerial(['lls_id'=>$lls_id])->delete();
            $hsl = $this->verif->createVerifManajerial($req->manajerial,['lls_id'=>$lls->lls_id]);
            $data['manajerial'] = $hsl->data;
        }
        if(isset($req->pajak)){
            $this->verif->getVerifPajak(['lls_id'=>$lls_id])->delete();
            $hsl = $this->verif->createVerifPajak($req->pajak,['lls_id'=>$lls->lls_id]);
            $data['pajak'] = $hsl->data;
        }
        if(isset($req->stafahli)){
            $this->verif->getVerifStaf(['lls_id'=>$lls_id])->delete();
            $hsl = $this->verif->createVerifStaf($req->stafahli,['lls_id'=>$lls->lls_id]);
            $data['stafahli'] = $hsl->data;
        }
        if(isset($req->pengalaman)){
            $this->verif->getVerifPengalaman(['lls_id'=>$lls_id])->delete();
            $hsl = $this->verif->createVerifPengalaman($req->pengalaman,['lls_id'=>$lls->lls_id]);
            $data['pengalaman'] = $hsl->data;
        }
        if(isset($req->peralatan)){
            $this->verif->getVerifPeralatan(['lls_id'=>$lls_id])->delete();
            $hsl = $this->verif->createVerifPeralatan($req->peralatan,['lls_id'=>$lls->lls_id]);
            $data['landasanhukum'] = $hsl->data;
        }
        if(isset($req->lainya)){
            $data['lainya'] = DokPenawaran::updateOrCreate(['psr_id'=>$psr->psr_id,'dok_jenis'=>6],['psr_id'=>$psr->psr_id,'dok_tgljam'=>\Carbon\Carbon::now(),'dok_jenis'=>6,'dok_id_attachment'=>$req->lainya['dok_id_attachment'],'audituser'=> $this->user['user']]);
        }
        DokPenawaran::updateOrCreate(['psr_id' => $psr->psr_id, 'dok_jenis' => 0], ['psr_id' => $psr->psr_id, 'dok_tgljam' => \Carbon\Carbon::now(), 'dok_jenis' => 0, 'audituser' => $this->user['user']]);
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function ins_penawaran($lls_id,Request $req){
        $psr = Peserta::where([['lls_id',$lls_id],['rkn_id',$this->user['user_id']]])->first();
        $data['peserta'] = $psr;
        $data['checklist'] = [];
        if(isset($req->peserta)){            
            if (array_key_exists('masa_berlaku_penawaran', $req->peserta)) {
                $inp = $req->peserta;
                $inp['tgl_surat_penawaran'] = \Carbon\Carbon::now();
                $psr->update($inp);
            } else {
                $psr->update($req->peserta);
            }
            $data['peserta'] = $psr;
        }
        if(isset($req->checklist['teknis'])){
            $teknis = [];
            foreach($req->checklist['teknis'] as $dp) {
                $dp['psr_id'] = $psr->psr_id;
                $dp['dok_jenis'] = 1;
                $dp['audituser'] = $this->user['user'];
                $dp['dok_tgljam'] = \Carbon\Carbon::now();
                array_push($teknis,DokPenawaran::updateOrCreate(['dok_jenis'=>1,'psr_id'=>$psr->psr_id,'chk_id'=>$dp['chk_id']],$dp));
            }
            $data['checklist']['teknis']=$teknis;
        }
        if(isset($req->checklist['harga'])){
            $harga = [];
            foreach($req->checklist['harga'] as $dp) {
                $dp['psr_id'] = $psr->psr_id;
                $dp['dok_jenis'] = 2;
                $dp['audituser'] = $this->user['user'];
                $dp['dok_tgljam'] = \Carbon\Carbon::now();
                array_push($harga,DokPenawaran::updateOrCreate(['dok_jenis'=>2,'psr_id'=>$psr->psr_id,'chk_id'=>$dp['chk_id']],$dp));
            }
            $data['checklist']['harga']=$harga;
        }        
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function extract_hps($lls_id){
        $psr = Peserta::where([['rkn_id','=', $this->user['user_id']],['lls_id','=',$lls_id]])->first();
        $arr = [];
        foreach($psr->psr_dkh as $key=>$val){
            unset($val['total_harga']);
            $arr[$key] = $val;
        }
        $export = new DkhExport($arr);
        return Excel::download($export, 'template-rincian.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    
    public function init_penawaran($lls_id){
        $lel = New NonLelController();
        $lel = $lel->update_penyedia($lls_id);
        $dok = DokNonLel::where('lls_id',$lel->lls_id)->first();
        $ctn = DokNonLelContent::where('dll_id',$dok->dll_id)->latest('dll_versi')->first();
        $psr = Peserta::where('lls_id',$lel->lls_id)->where('rkn_id',$this->user['user_id'])->first();
        if(isset($psr)){
            $psr->update(['psr_dkh'=> $this->transform_dkh($ctn->dll_dkh),'audituser'=>$this->user['user']]);
            return response()->json(['success'=>true,'data'=>$psr],201);
        }
        return response()->json(['success'=>false],401);
    }
    
    
    protected function check_pnwrn($dll){
        return Checklist::where('dll_id',$dll->dll_id)->select('checklist.*','dok_penawaran.dok_id_attachment')->leftJoin('checklist_master','checklist_master.ckm_id','=','checklist.ckm_id')->leftJoin('dok_penawaran','dok_penawaran.chk_id','=','checklist.chk_id')->orderBy('ckm_urutan', 'asc');
    }


    protected function transform_dkh($ori){
        $arr = [];
        foreach($ori as $key=>$val){
            $val['harga'] =  null;
            $val['total_harga'] =  null;
            $arr[$key] = $val;
        }
        return $arr;
    }
}
