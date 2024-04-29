<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NonLelSeleksi;
use App\Http\Controllers\Api\AuthController;

class PenilaianController extends Controller
{
    protected $user = null;
    
    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
    }
    
    public function penilaian($lls_id){
        $jenis = [0=>'Barang',1=>'Jasa Konsultansi Badan Usaha Non Konstruksi',2=>'Pekerjaan Konstruksi',3=>'Jasa Lainnya',4=>'Jasa Konsultansi Perorangan Non Konstruksi',5=>'Jasa Konsultansi Badan Usaha Konstruksi',6=>'Jasa Konsultansi Perorangan Konstruksi',7=>'Pekerjaan Konstruksi Terintegrasi'];
        $data['lelang'] = NonLelSeleksi::where('lls_id',$lls_id)->first();
        $data['lelang']['jenis'] = $jenis[$data['lelang']->kgr_id];
        $data['nilai'] = $this->nilai_kategori($data['lelang']->lls_id);
        $data['total'] = null!=$this->nilai_total($data['lelang']->lls_id) ? $this->nilai_total($data['lelang']->lls_id) : array('lls_id'=>$data['lelang']->lls_id,'ttl_bobot'=>null,'ttl_nilai'=>null);
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function detail_penilaian($lls_id,$ktr_id){
//        $ktr_id = $req->ktr_id;
        $data['lelang'] = NonLelSeleksi::where('lls_id',$lls_id)->first();
        $data['pertanyaan'] = $this->get_kategori(['ktr_id'=>$ktr_id])->select('kategori_master.ktr_id','kategori_master.ktr_nama',DB::raw('kategori_master.ktr_nilai as base_bobot'))->first();
        $data['jawaban'] = $this->option_kategori($ktr_id);
        $data['nilai'] = $this->get_nilai(['lls_id'=>$data['lelang']->lls_id,'ktr_id'=>$ktr_id])->first()!= null ? $this->get_nilai(['lls_id'=>$data['lelang']->lls_id,'ktr_id'=>$ktr_id])->select('penilaian_kinerja.lls_id','penilaian_kinerja.ktr_id','penilaian_kinerja.ref_id','penilaian_kinerja.inf_tamb')->first() : array('lls_id'=>null,'ktr_id'=>null,'ref_id'=>null,'inf_tamb'=>null);
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    public function ins_penilaian(Request $req){
        $ins = $req->nilai;
        $ind = $this->get_kategori(['ktr_id'=>$ins['ktr_id']])->first();
        $jwb = $this->get_kategori(['ktr_id'=>$ins['ref_id']])->first();
        $ins['base_nilai'] = $jwb->ktr_nilai; $ins['base_bobot'] = $ind->ktr_nilai; $ins['nilai'] = ($ind->ktr_nilai/100)*$jwb->ktr_nilai;
        $data['nilai'] = $this->insert_nilai($ins);
        return response()->json(['success'=>true,'data'=>$data],201);
    }
    
    protected function option_kategori($ktr_id){
        $prnt = $this->get_kategori(['ktr_id'=>$ktr_id])->first();
        return $this->get_kategori(['ktr_versi'=>$prnt->ktr_versi,'ktr_jenis'=>2])->select(DB::raw('kategori_master.ktr_id as ref_id'),'kategori_master.ktr_nama','kategori_master.ktr_uraian',DB::raw('kategori_master.ktr_nilai as base_nilai'))->orderBy('ktr_urutan', 'asc')->get();
    }
    
    protected function nilai_kategori($lls_id){
        $nilai = $this->get_nilai(['lls_id'=>$lls_id]);
        return $this->get_kategori(['kategori_master.ktr_jenis'=>1])->leftJoinSub($nilai, 'nilai', function(\Illuminate\Database\Query\JoinClause $join){
            $join->on('kategori_master.ktr_id','=','nilai.ktr_id');
        })->select('kategori_master.*','nilai.base_nilai',DB::raw('kategori_master.ktr_nilai as base_bobot'),'nilai.nilai')->get();
    }
    
    protected function nilai_total($lls_id){
        return $this->get_nilai(['penilaian_kinerja.lls_id'=>$lls_id])->select('penilaian_kinerja.lls_id',DB::raw('sum(penilaian_kinerja.base_bobot) as ttl_bobot'),DB::raw('sum(penilaian_kinerja.base_nilai) as ttl_nilai'))->groupBy('penilaian_kinerja.lls_id')->first();
    }
    
    protected function get_kategori($data){
        $tabel = 'kategori_master';
        return DB::table($tabel)->where($data);  
    }
    
    protected function get_nilai($data){
        $tabel = 'penilaian_kinerja';
        return DB::table($tabel)->where($data);  
    }
    
    protected function insert_nilai($data){
        $data['pnl_id'] = $this->generateID('penilaian_kinerja', 'pnl_id');
        DB::table('penilaian_kinerja')->updateOrInsert(['lls_id'=>$data['lls_id'],'ktr_id'=>$data['ktr_id']], $this->baseData($data));
        return $data;
    }
    
    private function baseData($data){
        $data['auditupdate'] = \Carbon\Carbon::now();
        $data['audituser'] = (isset($this->user))?$this->user['user']:'ADMIN';
        return $data;
    }
    
    private function generateID($table, $key) {
        do {
            $id = random_int("1" . str_repeat("0", 7), str_repeat("9", 8));
        }while(DB::table($table)->where($key,$id)->exists());
        return $id;
    }
}
