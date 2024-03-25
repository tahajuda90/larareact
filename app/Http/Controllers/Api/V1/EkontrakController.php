<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Riskihajar\Terbilang\Facades\Terbilang;
use App\Http\Controllers\Api\V1\AnggaranController;
use App\Models\Rekanan;
use App\Models\RknLndsnHukum;
use App\Models\Evaluasi;
use App\Models\Pegawai;
use App\Models\Sppbj;
use App\Models\Kontrak;
use App\Models\Spk;
use App\Models\Spmk;
use App\Models\NonLelSeleksi;
use App\Http\Controllers\Api\AuthController;

class EkontrakController extends Controller
{
    //
    
    protected $user = null;
    protected $ang;
    
    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
        $this->ang = new AnggaranController();
    }
    
    public function ekontrak($lls_id){
        $data['lelang'] = NonLelSeleksi::where('lls_id',$lls_id)->leftJoin('paket','nonlelang_seleksi.pkt_id','=','paket.pkt_id')->select('nonlelang_seleksi.*','paket.mtd_pemilihan')->first();
        $data['sppbj'] = $this->sppbj($lls_id);
        $data['kontrak'] = $this->kontrak($lls_id);
        $data['spk'] = $this->spk($lls_id);
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function init_sppbj($lls_id){
        $data['sppbj'] = array('sppbj_no'=>null,'sppbj_lamp'=>null,'sppbj_tgl_buat'=>null,'sppbj_kota'=>null,'jabatan_ppk_sppbj'=>null,'alamat_satker'=>null,'jaminan_pelaksanaan'=>null,'masa_berlaku_jaminan'=>null,'sppbj_attachment'=>null,'sppbj_tembusan'=>null,'peg_id'=>null,'psr_id'=>null);
        $data['lelang'] = NonLelSeleksi::where('lls_id',$lls_id)->first();
        $data['lelang']['anggaran'] = $this->ang->anggaranpkt($data['lelang']->pkt_id);
//        $data['mak'] = $data['lelang']['anggaran']->implode('ang_koderekening', ', ');
        $data['ppk'] = Pegawai::leftJoin('paket','paket.ppk_id','=','pegawai.peg_id')->where('paket.pkt_id',$data['lelang']->pkt_id)->select('pegawai.peg_id','pegawai.peg_nama','pegawai.peg_nip','pegawai.peg_jabatan')->first();
        $data['ppk']['satker'] = 'Rumah Sakit Gambiran Kota Kediri';
        $data['peserta'] = $this->short_peserta()->where(['evaluasi.lls_id'=>$lls_id])->get();
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function get_sppbj($sppbj_id){
        $data['sppbj'] = Sppbj::where('sppbj_id',$sppbj_id)->leftJoin('peserta_nonlelang',function($join){
            $join->on('sppbj.lls_id','=','peserta_nonlelang.lls_id');
            $join->on('sppbj.rkn_id','=','peserta_nonlelang.rkn_id');
        })->select('sppbj.*','peserta_nonlelang.psr_id')->first();
        $data['lelang'] = NonLelSeleksi::where('lls_id',$data['sppbj']->lls_id)->first();
        $data['lelang']['anggaran'] = $this->ang->anggaranpkt($data['lelang']->pkt_id);
        $data['ppk'] = Pegawai::leftJoin('paket','paket.ppk_id','=','pegawai.peg_id')->where('paket.pkt_id',$data['lelang']->pkt_id)->select('pegawai.peg_id','pegawai.peg_nama','pegawai.peg_nip','pegawai.peg_jabatan')->first();
        $data['ppk']['satker'] = 'Rumah Sakit Gambiran Kota Kediri';
        $data['peserta'] = $this->short_peserta()->where(['evaluasi.lls_id'=>$data['sppbj']->lls_id])->get();
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function insert_sppbj($lls_id,Request $req){
        $lel = NonLelSeleksi::where('lls_id',$lls_id)->leftJoin('paket','nonlelang_seleksi.pkt_id','=','paket.pkt_id')->select('nonlelang_seleksi.*','paket.mtd_pemilihan')->first();
        if(isset($req->sppbj)){
            $ins = $req->sppbj;
            $psr = $this->short_peserta()->where(['evaluasi.lls_id'=>$lls_id,'nilai_evaluasi.psr_id'=>$ins['psr_id']])->first();
            $ins['rkn_id'] = $psr->rkn_id;
            $ins['harga_final'] = $psr->nev_harga_negosiasi;
        }
        $ins['lls_id'] = $lel->lls_id;
        $ins['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN';        
        $data = Sppbj::create($ins);
        switch ($lel->mtd_pemilihan) {
            case 0:
                $this->create_spk($data);
                break;
            case 1:
                $this->create_kontrak($data);
                break;
        }
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function update_sppbj($sppbj_id,Request $req){
        $data = Sppbj::where('sppbj_id',$sppbj_id)->first();
        if(isset($req->sppbj)){
            $ins = $req->sppbj;
            $psr = $this->short_peserta()->where(['evaluasi.lls_id'=>$data->lls_id,'nilai_evaluasi.psr_id'=>$ins['psr_id']])->first();
            $ins['rkn_id'] = $psr->rkn_id;
            $ins['harga_final'] = $psr->nev_harga_negosiasi;
            $ins['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN'; 
            $data->update($ins);
        }
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function cetak_sppbj($sppbj_id){
        $data['sppbj'] = Sppbj::where('sppbj_id',$sppbj_id)->leftJoin('peserta_nonlelang',function($join){
            $join->on('sppbj.lls_id','=','peserta_nonlelang.lls_id');
            $join->on('sppbj.rkn_id','=','peserta_nonlelang.rkn_id');
        })->select('sppbj.*','peserta_nonlelang.psr_id')->first();
        $data['lelang'] = NonLelSeleksi::where('lls_id',$data['sppbj']->lls_id)->first();
        $data['peserta'] = $this->short_peserta()->where(['evaluasi.lls_id'=>$data['sppbj']->lls_id,'peserta_nonlelang.psr_id'=>$data['sppbj']->psr_id])->first();
//        return response()->json(['success'=>true,'data'=>$data],201);
//        return view('template.SPPBJ',$data);
        $pdf = PDF::loadview('template.SPPBJ',$data);
        $pdf->setPaper('legal','potrait');
        return $pdf->stream('SPPBJ-'.$data['lelang']->lls_id.'.pdf');
    }
    
    public function get_kontrak($kontrak_id){
        $data['kontrak'] = Kontrak::where('kontrak_id',$kontrak_id)->first();
        $data['lelang'] = NonLelSeleksi::where('lls_id',$data['kontrak']->lls_id)->leftJoin('paket','nonlelang_seleksi.pkt_id','=','paket.pkt_id')->select('nonlelang_seleksi.*','paket.lls_kontrak_pekerjaan')->first();
        $data['sppbj'] = Sppbj::where('lls_id',$data['lelang']->lls_id)->select('sppbj_id','alamat_satker')->first();
        $data['sppbj']['satker'] = 'Rumah Sakit Gambiran Kota Kediri';
        $data['rekanan'] = Rekanan::where('rkn_id',$data['kontrak']->rkn_id)->select('rkn_id','rkn_nama','rkn_alamat')->first();
        $data['rekanan']['akta'] = RknLndsnHukum::where('rkn_id',$data['rekanan']->rkn_id)->first();
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function update_kontrak($kontrak_id,Request $req){
        $data = Kontrak::where('kontrak_id',$kontrak_id)->first();
        if(isset($req->kontrak)){
            $ins = $req->kontrak;
            $ins['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN'; 
            $data->update($ins);
        }
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function cetak_kontrak($kontrak_id){
        $jenis = [0=>'Barang',1=>'Jasa Konsultansi Badan Usaha Non Konstruksi',2=>'Pekerjaan Konstruksi',3=>'Jasa Lainnya',4=>'Jasa Konsultansi Perorangan Non Konstruksi',5=>'Jasa Konsultansi Badan Usaha Konstruksi',6=>'Jasa Konsultansi Perorangan Konstruksi',7=>'Pekerjaan Konstruksi Terintegrasi'];
        $kontrak = [1=>'Lumsum',2=>'Harga Satuan',3=>'Gabungan Lumsum & Harga Satuan',4=>'Putar Kunci',10=>'Kontrak Payung',13=>'Waktu Penugasan',14=>'Biaya Plus Imbalan'];
        $data['kontrak'] = Kontrak::where('kontrak_id',$kontrak_id)->first();
        $data['kontrak']['kontrak_nilai_abc'] = Terbilang::make($data['kontrak']->kontrak_nilai);
        $data['sppbj'] = Sppbj::where('lls_id',$data['kontrak']->lls_id)->first();
        $data['lelang'] = NonLelSeleksi::where('lls_id',$data['kontrak']->lls_id)->leftJoin('paket','nonlelang_seleksi.pkt_id','=','paket.pkt_id')->select('nonlelang_seleksi.*','paket.lls_kontrak_pekerjaan')->first();
        $data['lelang']['jenis'] = $jenis[$data['lelang']->kgr_id];
        $data['lelang']['kontrak'] = $kontrak[$data['lelang']->lls_kontrak_pekerjaan];
        $data['rekanan'] = Rekanan::where('rkn_id',$data['kontrak']->rkn_id)->select('rkn_id','rkn_nama','rkn_alamat')->first();
        $data['rekanan']['akta'] = RknLndsnHukum::where('rkn_id',$data['rekanan']->rkn_id)->first();
        $pdf = PDF::loadview('template.KONTRAK',$data);
        $pdf->setPaper('legal','potrait');
        return $pdf->stream('PERJANJIAN-'.$data['lelang']->lls_id.'.pdf');
    }
    
    public function get_spk($spk_id){
        $data['spk'] = Spk::where('spk_id',$spk_id)->first();
        $data['lelang'] = NonLelSeleksi::where('lls_id',$data['spk']->lls_id)->first();
        $data['ppk'] = Pegawai::where('peg_id',$data['spk']->ppk_id)->select('peg_nama','peg_nip','peg_jabatan','peg_pangkat','peg_golongan')->first();
        $data['rekanan'] = Rekanan::where('rkn_id',$data['spk']->rkn_id)->select('rkn_id','rkn_nama','rkn_alamat')->first();
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function update_spk($spk_id,Request $req){
        $data = Spk::where('spk_id',$spk_id)->first();
        if(isset($req->spk)){
            $ins = $req->spk;
            $ins['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN'; 
            $data->update($ins);
        }
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function cetak_spk($spk_id){
        $jenis = [0=>'Barang',1=>'Jasa Konsultansi Badan Usaha Non Konstruksi',2=>'Pekerjaan Konstruksi',3=>'Jasa Lainnya',4=>'Jasa Konsultansi Perorangan Non Konstruksi',5=>'Jasa Konsultansi Badan Usaha Konstruksi',6=>'Jasa Konsultansi Perorangan Konstruksi',7=>'Pekerjaan Konstruksi Terintegrasi'];
        $kontrak = [1=>'Lumsum',2=>'Harga Satuan',3=>'Gabungan Lumsum & Harga Satuan',4=>'Putar Kunci',10=>'Kontrak Payung',13=>'Waktu Penugasan',14=>'Biaya Plus Imbalan'];
        $data['spk'] = Spk::where('spk_id',$spk_id)->first();
        $data['spk']['spk_nilai_abc'] = Terbilang::make($data['spk']->spk_nilai);
        $data['lelang'] = NonLelSeleksi::where('lls_id',$data['spk']->lls_id)->leftJoin('paket','nonlelang_seleksi.pkt_id','=','paket.pkt_id')->select('nonlelang_seleksi.*','paket.lls_kontrak_pekerjaan')->first();
        $data['lelang']['jenis'] = $jenis[$data['lelang']->kgr_id];
        $data['lelang']['kontrak'] = $kontrak[$data['lelang']->lls_kontrak_pekerjaan];
        $data['rekanan'] = Rekanan::where('rkn_id',$data['spk']->rkn_id)->select('rkn_id','rkn_nama','rkn_alamat')->first();
        $data['rekanan']['akta'] = RknLndsnHukum::where('rkn_id',$data['rekanan']->rkn_id)->first();
        $pdf = PDF::loadview('template.SPK',$data);
        $pdf->setPaper('legal','potrait');
        return $pdf->stream('SPK-'.$data['lelang']->lls_id.'.pdf');
    }
    
    protected function create_kontrak($sppbj){
        $lel = NonLelSeleksi::where('lls_id',$sppbj->lls_id)->first();
        $ang = $this->ang->anggaranpkt($lel->pkt_id)->implode('ang_koderekening', ', ');
        $ppk = Pegawai::leftJoin('paket','paket.ppk_id','=','pegawai.peg_id')->leftJoin('nonlelang_seleksi','nonlelang_seleksi.pkt_id','=','paket.pkt_id')->where('nonlelang_seleksi.lls_id',$sppbj->lls_id)->select('pegawai.peg_id','pegawai.peg_nama','pegawai.peg_nip','pegawai.peg_jabatan')->first();
        $ins = array('lls_id'=>$sppbj->lls_id,'rkn_id'=>$sppbj->rkn_id,'kontrak_nilai'=>$sppbj->harga_final,'nama_ppk_kontrak'=>$ppk->peg_nama,'no_sk_ppk_kontrak'=>$ppk->peg_no_sk,'nip_ppk_kontrak'=>$ppk->peg_nip,'jabatan_ppk_kontrak'=>$sppbj->jabatan_ppk_sppbj,'kode_akun_kegiatan'=>$ang,'no_skpemenang'=>$sppbj->sppbj_no,'tgl_skpemenang'=>$sppbj->sppbj_tgl_buat);
        $ins['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN'; 
        Kontrak::create($ins);
    }
    
    protected function create_spk($sppbj){
        $ppk = Pegawai::leftJoin('paket','paket.ppk_id','=','pegawai.peg_id')->leftJoin('nonlelang_seleksi','nonlelang_seleksi.pkt_id','=','paket.pkt_id')->where('nonlelang_seleksi.lls_id',$sppbj->lls_id)->select('pegawai.peg_id','pegawai.peg_nama','pegawai.peg_nip','pegawai.peg_jabatan')->first();
        $ins = array('lls_id'=>$sppbj->lls_id,'rkn_id'=>$sppbj->rkn_id,'spk_nilai'=>$sppbj->harga_final,'nama_ppk_kontrak'=>$ppk->peg_nama,'no_sk_ppk_kontrak'=>$ppk->peg_no_sk,'nip_ppk_kontrak'=>$ppk->peg_nip,'jabatan_ppk_kontrak'=>$sppbj->jabatan_ppk_sppbj,'ppk_id'=>$sppbj->peg_id);
        $ins['spk_content'] = array('tgl_brng_diterima'=>null,'waktu_penyelesaian'=>null,'tgl_pekerjaan_selesai'=>null,'kota_pesanan'=>null);
        Spk::create($ins);
    }
    
    protected function sppbj($lls_id){
        $db = Sppbj::where('lls_id',$lls_id)->leftJoin('rekanan','rekanan.rkn_id','=','sppbj.rkn_id')->select('sppbj.*','rekanan.rkn_nama')->first();
        $data = (isset($db)) ? $db : array('sppbj_id'=>null,'sppbj_no'=>null,'rkn_nama'=>null);
        return $data;
    }
    
    protected function kontrak($lls_id){
        $db = Kontrak::where('lls_id',$lls_id)->first();
        $data = (isset($db)) ? $db : array('kontrak_id'=>null,'kontrak_nilai'=>null,'kontrak_sskk_attachment'=>null);
        return $data;
    }
    
    protected function spk($lls_id){
        $db = Spk::where('lls_id',$lls_id)->first();
        $data = (isset($db)) ? $db : array('spk_id'=>null,'spk_nilai'=>null,'kontrak_sskk_attachment'=>null);
        return $data;
    }
    
    protected function spmk($lls_id){
        $kntrk = Spmk::leftJoin('kontrak','kontrak.kontrak_id','=','pesanan.kontrak_id')->where('kontrak.lls_id',$lls_id)->select('pesanan.*')->first();
        $spk = Spmk::leftJoin('spk','spk.spk_id','=','pesanan.spk_id')->where('spk.lls_id',$lls_id)->select('pesanan.*')->first();
        if(isset($kntrk)){
            $data = $kntrk;
        }elseif (isset ($spk)) {
            $data = $spk;
        }else{
            $data = array('pes_id'=>null,'pes_no'=>null);
        }
        return $data;
    }
    
    protected function short_peserta(){
        $db = Evaluasi::where(['evaluasi.eva_jenis'=>4])->leftJoin('nilai_evaluasi','nilai_evaluasi.eva_id','=','evaluasi.eva_id')
                ->leftJoin('peserta_nonlelang','peserta_nonlelang.psr_id','=','nilai_evaluasi.psr_id')->leftJoin('rekanan','peserta_nonlelang.rkn_id','=','rekanan.rkn_id')->select('peserta_nonlelang.psr_id','rekanan.rkn_id','rekanan.rkn_nama','rekanan.rkn_npwp','nilai_evaluasi.nev_harga_negosiasi','nilai_evaluasi.nev_harga_terkoreksi','nilai_evaluasi.nev_harga');
        return $db;
    }
    
}
