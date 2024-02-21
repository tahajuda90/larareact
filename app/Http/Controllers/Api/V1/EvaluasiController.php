<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\NonLelSeleksi;
use App\Models\DokNonLel;
use App\Models\DokNonLelContent;
use App\Models\DokPenawaran;
use App\Models\Evaluasi;
use App\Models\Peserta;
use App\Models\Rekanan;
use App\Models\NilaiEval;
use App\Models\BeritaAcara;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\VerifController;
use App\Http\Controllers\Api\V1\NonLelController;

class EvaluasiController extends Controller
{
    //
    protected $user;
    protected $verif;
    protected  $lel;


    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
        $this->verif = new VerifController();
        $this->lel = new NonLelController();
    }
    
    
    public function get_lelang($lls_id){
        $data['lelang'] = NonLelSeleksi::where('lls_id',$lls_id)->select('nonlelang_seleksi.*',DB::raw('tahap_nama_now ( nonlelang_seleksi.lls_id, now( ) :: TIMESTAMP ) as tahapan'),DB::raw('tahap_now ( nonlelang_seleksi.lls_id, now( ) :: TIMESTAMP ) as tahapan2'))->first();
        $data['dokumen'] = DokNonLel::where('lls_id',$data['lelang']->lls_id)->first();
        $data['peserta'] = Peserta::where('lls_id',$data['lelang']->lls_id)->select('peserta_nonlelang.*','rekanan.rkn_nama')->leftJoin('rekanan','rekanan.rkn_id','=','peserta_nonlelang.rkn_id')->first();
        $data['evaluasi'] = ['kualifikasi'=> $this->get_eval($data['lelang']->lls_id,0),
            'administrasi'=> $this->get_eval($data['lelang']->lls_id,1),
            'teknis'=> $this->get_eval($data['lelang']->lls_id,2),
            'harga'=> $this->get_eval($data['lelang']->lls_id,3),
            'pemenang'=>$this->getPemenang($data['lelang']->lls_id)
            ];
        $data['berita'] = $this->get_berAcara($data['lelang']->lls_id);
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function get_kualifikasi($psr_id){
        $psr = Peserta::where('psr_id',$psr_id)->first();
        $data['identitas'] = Rekanan::where('rkn_id',$psr->rkn_id)->first();
        $data['izinusaha'] = $this->verif->getVerifIjin([['lls_id', '=', $psr->lls_id]])->leftJoin('ijin_usaha_rekanan','ijin_usaha_rekanan.ius_id','=','verif_ijin_usaha.ius_id')->where([['ijin_usaha_rekanan.rkn_id','=',$psr->rkn_id]])->select('ijin_usaha_rekanan.*')->get();
        $data['landasanhukum'] = $this->verif->getVerifLhk([['lls_id', '=', $psr->lls_id]])->leftJoin('landasan_hukum_rekanan','landasan_hukum_rekanan.lhkp_id','=','verif_lhk.lhkp_id')->where([['landasan_hukum_rekanan.rkn_id','=',$psr->rkn_id]])->select('landasan_hukum_rekanan.*')->get();
        $data['manajerial'] = $this->verif->getVerifManajerial([['lls_id', '=', $psr->lls_id]])->leftJoin('manajerial_rekanan','manajerial_rekanan.id_manajerial','=','verif_manajerial.id_manajerial')->where([['manajerial_rekanan.rkn_id','=',$psr->rkn_id]])->select('manajerial_rekanan.*')->get();
        $data['pajak'] = $this->verif->getVerifPajak([['lls_id', '=', $psr->lls_id]])->leftJoin('pajak','pajak.pjk_id','=','verif_pajak.pjk_id')->where([['pajak.rkn_id','=',$psr->rkn_id]])->select('pajak.*')->get();
        $data['stafahli'] = $this->verif->getVerifStaf([['lls_id', '=', $psr->lls_id]])->leftJoin('staf_ahli_rekanan','staf_ahli_rekanan.stp_id','=','verif_stafahli.stp_id')->where([['staf_ahli_rekanan.rkn_id','=',$psr->rkn_id]])->select('staf_ahli_rekanan.*')->get();
        $data['pengalaman'] = $this->verif->getVerifPengalaman([['lls_id', '=', $psr->lls_id]])->leftJoin('pengalaman_rekanan','pengalaman_rekanan.pen_id','=','verif_pengalaman.pen_id')->where([['pengalaman_rekanan.rkn_id','=',$psr->rkn_id]])->select('pengalaman_rekanan.*')->get();
        $data['peralatan'] = $this->verif->getVerifPeralatan([['lls_id', '=', $psr->lls_id]])->leftJoin('peralatan_rekanan','peralatan_rekanan.id_prl','=','verif_peralatan.id_prl')->where([['peralatan_rekanan.rkn_id','=',$psr->rkn_id]])->select('peralatan_rekanan.*')->get();
        $data['lainya'] = DokPenawaran::where([['psr_id', '=', $psr->psr_id],['dok_jenis', '=', 6]])->first();
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function get_penawaran($psr_id){
        $psr = Peserta::select('peserta_nonlelang.*','rekanan.rkn_nama','nonlelang_seleksi.pkt_nama')->where('psr_id',$psr_id)->leftJoin('rekanan','rekanan.rkn_id','=','peserta_nonlelang.rkn_id')->leftJoin('nonlelang_seleksi','nonlelang_seleksi.lls_id','=','peserta_nonlelang.lls_id')->first();
        $data['dok_lelang'] = DokNonLelContent::select('dok_nonlelang_content.*')->leftJoin('dok_nonlelang','dok_nonlelang_content.dll_id','=','dok_nonlelang.dll_id')->latest('dok_nonlelang_content.dll_versi')->where('dok_nonlelang.lls_id',$psr->lls_id)->first();
        $data['peserta'] = $psr;
        $data['teknis'] = $this->base_penawaran($data['peserta']->psr_id)->where('dok_jenis', 1)->get();
        $data['harga'] = $this->base_penawaran($data['peserta']->psr_id)->where('dok_jenis', 2)->get();
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function base_eval($psr_id){
        $data['peserta'] = Peserta::select('peserta_nonlelang.*','rekanan.rkn_nama','nonlelang_seleksi.pkt_nama')->where('psr_id',$psr_id)->leftJoin('rekanan','rekanan.rkn_id','=','peserta_nonlelang.rkn_id')->leftJoin('nonlelang_seleksi','nonlelang_seleksi.lls_id','=','peserta_nonlelang.lls_id')->first();
        $lel = $this->lel->get_dokumen($data['peserta']->lls_id, false);
        $data['checklist'] = $lel['checklist'];
        $data['kualifikasi'] = $this->get_nilaieval($data['peserta']->psr_id, 0);
        $data['administrasi'] = $this->get_nilaieval($data['peserta']->psr_id, 1);
        $data['teknis'] = $this->get_nilaieval($data['peserta']->psr_id, 2);
        $data['harga'] = $this->get_nilaieval($data['peserta']->psr_id, 3);
        $data['negosiasi'] = (!empty($this->get_nilaieval($data['peserta']->psr_id, 4))) ? $this->get_nilaieval($data['peserta']->psr_id, 4) : ['nev_dkh'=>null,'nev_urutan'=>null,'nev_harga'=>null,'nev_harga_terkoreksi'=>null,'nev_harga_negosiasi'=>null];
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    
    public function do_eval($psr_id,Request $req){
        $psr = Peserta::where('psr_id',$psr_id)->first();
        if(isset($req->kualifikasi)){
            $nil = NilaiEval::where('psr_id',$psr->psr_id)->leftJoin('evaluasi','evaluasi.eva_id','=','nilai_evaluasi.eva_id')->where(['evaluasi.eva_jenis'=>0],['lls_id'=>$psr->lls_id])->select('nilai_evaluasi.*')->first();
            $data = $this->ins_eval($nil, $psr, $req->kualifikasi, 'kualifikasi');
        }        
        if(isset($req->administrasi)){
            $nil = NilaiEval::where('psr_id',$psr->psr_id)->leftJoin('evaluasi','evaluasi.eva_id','=','nilai_evaluasi.eva_id')->where(['evaluasi.eva_jenis'=>1],['lls_id'=>$psr->lls_id])->select('nilai_evaluasi.*')->first();
            $data = $this->ins_eval($nil, $psr, $req->administrasi, 'administrasi');
        }        
        if(isset($req->teknis)){
            $nil = NilaiEval::where('psr_id',$psr->psr_id)->leftJoin('evaluasi','evaluasi.eva_id','=','nilai_evaluasi.eva_id')->where(['evaluasi.eva_jenis'=>2],['lls_id'=>$psr->lls_id])->select('nilai_evaluasi.*')->first();
            $data = $this->ins_eval($nil, $psr, $req->teknis, 'teknis');
        }        
        if(isset($req->harga)){
            $nil = NilaiEval::where('psr_id',$psr->psr_id)->leftJoin('evaluasi','evaluasi.eva_id','=','nilai_evaluasi.eva_id')->where(['evaluasi.eva_jenis'=>3],['lls_id'=>$psr->lls_id])->select('nilai_evaluasi.*')->first();
            $data = $this->ins_eval($nil, $psr, $req->harga, 'harga');
        }
        if(isset($req->negosiasi)){
            $nil = NilaiEval::where('psr_id',$psr->psr_id)->leftJoin('evaluasi','evaluasi.eva_id','=','nilai_evaluasi.eva_id')->where(['evaluasi.eva_jenis'=>4],['lls_id'=>$psr->lls_id])->select('nilai_evaluasi.*')->first();
            $data = $this->ins_nego($nil, $psr, $req->negosiasi);
        }
        return response()->json(['success'=>true,'data'=>$data],201);
    }

    
    
    //create
    
    protected function ins_eval($nil, $psr, $req, $jns) {
        $jenis = ['kualifikasi'=>0,'administrasi'=>1,'teknis'=>2,'harga'=>3,'negosiasi'=>4];
        $chk = $req['chk']; unset($req['chk']);
        $nil_update = $req;
        $nil_update['nev_lulus'] = (isset($nil_update['nev_uraian'])) ? 0:1;
        if($jns == 'harga'){
            Peserta::where('psr_id',$psr->psr_id)->first()->update(['psr_harga_terkoreksi'=>$nil_update['nev_harga_terkoreksi']]);
            $nil_update['nev_harga'] = $psr->psr_harga;
        }        
        if (!empty($nil)) {
            $data[$jns] = $this->updateNilai($nil->nev_id, $nil_update);
            $data[$jns]['chk'] = $this->create_chk($nil->nev_id, $chk);
        } else {
            $eval = Evaluasi::create(['lls_id' => $psr->lls_id, 'eva_status' => 1, 'eva_jenis' => $jenis[$jns], 'eva_tgl_setuju' => \Carbon\Carbon::now(), 'eva_versi' => 1,'audituser'=>(isset($this->user))?$this->user['user']:'ADMIN']);
            $nil_update['eva_id'] = $eval->eva_id;
            $nil_update['psr_id'] = $psr->psr_id;
            $nil_update['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN';
            $data[$jns] = NilaiEval::create($nil_update);
            $data[$jns]['chk'] = $this->create_chk($data['kualifikasi']->nev_id, $chk);
        }
        return $data;
    }
    
    protected function ins_nego($nil, $psr, $req){
        $nil_update = $req;
        $nil_update['nev_lulus'] = (isset($nil_update['nev_uraian'])) ? 0:1;
        $nil_update['nev_harga'] = $psr->psr_harga;
        $nil_update['nev_harga_terkoreksi'] = $psr->psr_harga_terkoreksi;
        if(!empty($nil)){
            $data['negosiasi'] = $this->updateNilai($nil->nev_id, $nil_update);
        }else{
            $eval = Evaluasi::create(['lls_id' => $psr->lls_id, 'eva_status' => 1, 'eva_jenis' => 4, 'eva_tgl_setuju' => \Carbon\Carbon::now(), 'eva_versi' => 1,'audituser'=>(isset($this->user))?$this->user['user']:'ADMIN']);
            $nil_update['eva_id'] = $eval->eva_id;
            $nil_update['psr_id'] = $psr->psr_id;
            $nil_update['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN';
            $data['negosiasi'] = NilaiEval::create($nil_update);
        }
        return $data;
    }

    protected function create_chk($nev_id,$chk){
        $tabel = 'checklist_evaluasi';
        $hasil = [];
        DB::table($tabel)->where('nev_id',$nev_id)->delete();
        if(isset($chk)){
            foreach ($chk as $ck) {
                $ck['nev_id'] = $nev_id;
                $ck['audituser'] = (isset($this->user)) ? $this->user['user'] : 'ADMIN';
                (DB::table($tabel)->updateOrInsert($ck)) ? array_push($hasil, $ck) : '';
            }
        }        
        return $hasil;
    }
    
    protected function updateNilai($nev_id,$data){
        $new = NilaiEval::where('nev_id',$nev_id)->first();
        $data['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN';
        $new->update($data);
        return $new;
    }
    
    //get
    protected function get_berAcara($lls_id){
        $lel = NonLelSeleksi::where('lls_id',$lls_id)->leftJoin('paket','paket.pkt_id','=','nonlelang_seleksi.pkt_id')->select('nonlelang_seleksi.*','paket.mtd_pemilihan')->first();
        $data = [
                'BA_EVALUASI_KUALIFIKASI'=> $this->get_baseBerAcara('BA_EVALUASI_KUALIFIKASI',$lel->lls_id),
                'BA_EVALUASI_PENAWARAN'=> $this->get_baseBerAcara('BA_EVALUASI_PENAWARAN', $lel->lls_id),
                'BA_HASIL_LELANG'=> $this->get_baseBerAcara('BA_HASIL_LELANG', $lel->lls_id),
                'BA_TAMBAHAN'=> $this->get_baseBerAcara('BA_TAMBAHAN', $lel->lls_id)
            ];
        $data['mode'] = ($lel->mtd_pemilihan == 1) ? true : false;
        return $data;
    }
    
    protected function get_baseBerAcara($jns,$lls_id){
        $bc = BeritaAcara::where(['brc_jenis_ba'=>$jns,'lls_id'=>$lls_id])->first();
        $init = ['brc_jenis_ba'=>$jns,'brt_no'=>null,'brt_tgl_evaluasi'=>null,'brt_info'=>null,'brc_id_attachment'=>null];
        return (isset($bc)) ? $bc : $init;
    }


    protected function base_penawaran($psr_id){
        return DokPenawaran::where('psr_id',$psr_id)->leftJoin('checklist','checklist.chk_id','=','dok_penawaran.chk_id')->select('dok_penawaran.*','checklist.chk_nama');
    }    
    
    protected function get_nilaieval($psr_id,$jns){
        $data = NilaiEval::whereHas('eval',function(Builder $query) use($jns){
            $query->where('evaluasi.eva_jenis',$jns);
        })->where('psr_id',$psr_id)->first();
        if($jns == 4){
            return $data;
        }        
        (isset($data->nev_id)) ? $data['chk'] = $this->get_checkEval($data->nev_id) : null;
        return $data;
    }
    
    protected function get_checkEval($nev_id){
        $tabel = 'checklist_evaluasi';
        $hasil = DB::table($tabel)->where('nev_id',$nev_id)->get();
        return (count($hasil)>0) ? $hasil : null;
    }
    
    
    protected function getPemenang($lls_id){
        return Evaluasi::where(['evaluasi.eva_jenis'=>4,'evaluasi.lls_id'=>$lls_id])->leftJoin('nilai_evaluasi','evaluasi.eva_id','=','nilai_evaluasi.eva_id')->where(['nilai_evaluasi.nev_urutan'=>1])
                ->leftJoin('peserta_nonlelang','nilai_evaluasi.psr_id','=','peserta_nonlelang.psr_id')->leftJoin('rekanan','rekanan.rkn_id','=','peserta_nonlelang.rkn_id')->select('evaluasi.eva_status','nilai_evaluasi.nev_lulus','nilai_evaluasi.nev_urutan','nilai_evaluasi.nev_harga_terkoreksi','rekanan.rkn_nama')->first();
    }

    protected function get_eval($lls_id,$eva_jns){
        return Evaluasi::where(['eva_jenis'=>$eva_jns,'lls_id'=>$lls_id])->leftJoin('nilai_evaluasi','evaluasi.eva_id','=','nilai_evaluasi.eva_id')->select('evaluasi.eva_status','nilai_evaluasi.nev_lulus','nilai_evaluasi.nev_urutan','nilai_evaluasi.nev_harga_terkoreksi')->first();
    }   



    private function baseData($data){
        $data['auditupdate'] = \Carbon\Carbon::now();
        $data['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN';
        return $data;
    }
    
    
    private function generateID($table, $key) {
        do {
            $id = random_int("1" . str_repeat("0", 7), str_repeat("9", 8));
        } while (DB::table($table)->where($key, $id)->exists());
        return $id;
    }
}
