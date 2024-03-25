<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Models\BeritaAcara;
use App\Models\NonLelSeleksi;
use App\Models\Peserta;
use App\Models\Evaluasi;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\NonLelController;

class BerAcaraController extends Controller
{
    
    protected $user;
    protected  $lel;
    
    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
        $this->lel = new NonLelController();
    }
    
    public function createBerita($lls_id,Request $req){
        $lel = NonLelSeleksi::where('lls_id',$lls_id)->first();  
        $keys = array_keys($req->berita);
        foreach($keys as $key){
            $ins = $req->berita[$key];
            $ins['audituser'] = $this->user['user'];
            $data[$key] = BeritaAcara::updateOrCreate(['lls_id'=>$lel->lls_id,'brc_jenis_ba'=>$key],$ins);
        }        
        return response()->json(['success'=>true,'data'=>$data],201);
    }

        
    public function cetak($brc_id){
        $jp = ['Pengadaan Barang','Jasa Konsultansi Badan Usaha Non Konstruksi','Pekerjaan Konstruksi','Jasa Lainnya','Jasa Konsultansi Perorangan Non Konstruksi','Jasa Konsultansi Badan Usaha Konstruksi','Jasa Konsultansi Perorangan Konstruksi','Pekerjaan Konstruksi Terintegrasi'];
        $brc = BeritaAcara::where('brc_id',$brc_id)->first();
        $data['brc'] = $brc;
        $data['lel'] = NonLelSeleksi::where('lls_id',$brc->lls_id)->first();
        $data['lel']->jenis = $jp[$data['lel']->kgr_id];
        $data['peserta'] = Peserta::where('lls_id',$data['lel']->lls_id)->select('peserta_nonlelang.*','rekanan.rkn_nama')->leftJoin('rekanan','rekanan.rkn_id','=','peserta_nonlelang.rkn_id')->first();
        $data['nilai']['kualifikasi'] = $this->get_eval($data['lel']->lls_id, 0);
        $data['nilai']['administrasi'] = $this->get_eval($data['lel']->lls_id, 1);
        $data['nilai']['teknis'] = $this->get_eval($data['lel']->lls_id, 2);
        $data['nilai']['harga'] = $this->get_eval($data['lel']->lls_id, 3);
        $data['nilai']['pemenang'] = $this->get_eval($data['lel']->lls_id, 4);
        $pdf = PDF::loadview('template.'.$brc->brc_jenis_ba,$data);
        $pdf->setPaper('legal','potrait');
        return $pdf->stream($brc->brc_jenis_ba.'-'.$data['lel']->lls_id.'.pdf');
    }
    
    protected function get_eval($lls_id,$eva_jns){
        return Evaluasi::where(['eva_jenis'=>$eva_jns,'lls_id'=>$lls_id])->leftJoin('nilai_evaluasi','evaluasi.eva_id','=','nilai_evaluasi.eva_id')->select('evaluasi.eva_status','nilai_evaluasi.nev_lulus','nilai_evaluasi.nev_urutan','nilai_evaluasi.nev_harga_terkoreksi','nilai_evaluasi.nev_harga','nilai_evaluasi.nev_harga_negosiasi')->first();
    }
    
}
