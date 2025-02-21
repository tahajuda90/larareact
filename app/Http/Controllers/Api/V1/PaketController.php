<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\IntegrasiSirup;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Api\V1\AnggaranController;
use App\Http\Controllers\Api\V1\DokPersiapanController;
use App\Http\Controllers\Api\AuthController;
use App\Models\Paket;
use App\Models\PktLokasi;
use App\Models\Anggaran;
//use App\Models\Pegawai;
//use App\Models\Panitia;

class PaketController extends Controller
{
    //

    protected $ang;
    protected $rup;
    protected $dok;
    protected $user = null;
    
    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
        $this->rup = New IntegrasiSirup();
        $this->ang = new AnggaranController();
        $this->dok = new DokPersiapanController();
    }
    
    
    //create paket
    public function inisiasi_paket(Request $req){
        $kode = $req->rup;
        if (!empty($this->rup->rup_integrate($kode))) {
            $rupdta = (object) $this->rup->rup_integrate($kode);
            $anggaran = $this->ang->create(['rup_id' => $rupdta->rup_id, 'sbd_id' => $rupdta->sbd_id, 'ang_koderekening' => $rupdta->angg_rekening,
                'ang_nilai' => $rupdta->angg_nilai, 'ang_uraian' => $rupdta->uraian, 'ang_tahun' => $rupdta->angg_thn, 'ang_nama' => $rupdta->pkt_nama,'lokasi'=>$rupdta->lokasi]);
//        print_r($anggaran->ang_id);
            $paket = $this->create_paket(['pkt_nama' => $rupdta->pkt_nama, 'pkt_pagu' => $rupdta->angg_nilai, 'pkt_status' => 0,'ppk_id'=>$this->user['user_id'],'audituser'=>(isset($this->user))?$this->user['user']:'ADMIN']);
            $this->dok->createDok(['pkt_id' => $paket->pkt_id, 'dp_versi' => 1]);
            $this->create_paketangg(['pkt_id' => $paket->pkt_id, 'ang_id' => $anggaran->ang_id]);
            $this->create_paketlokasi(['prop' => $anggaran->lokasi['propinsi'], 'kota' => $anggaran->lokasi['kota'], 'pkt_lokasi' => $anggaran->lokasi['lokasi'], 'pkt_id' => $paket->pkt_id]);
//        $data = ['paket'=>$paket,'anggaran'=>$anggaran,'lokasi'=>$rupdta->lokasi,'dok_persiapan'=>$dokumen];
            $data = ['pkt_id' => $paket->pkt_id, 'rup' => $rupdta];
            return response()->json(['success' => true,'data' => $data,], 201);
        }
        return response()->json(['success' => false], 404);
    }
    
    //get paket
    public function get_paket($pkt_id){
        $paket = new Paket();
//        $data = ['paket'=>$paket->select('paket.*','nonlelang_seleksi.lls_id')->leftJoin('nonlelang_seleksi','nonlelang_seleksi.pkt_id','=','paket.pkt_id')->where('paket.pkt_id',$pkt_id)->first()];
        $anggaran = $this->ang->anggaranpkt($pkt_id);
        $lokasi = $this->get_paketlokasi($pkt_id);
        $dokumen = $this->dok->dokpkt($pkt_id);
        $pj = $paket->where('pkt_id',$pkt_id)->with('panitia','pp')->first();
        $data = ['paket'=>$paket->where('pkt_id',$pkt_id)->first(),'anggaran'=>$anggaran,'lokasi'=>$lokasi,'dok_persiapan'=>$dokumen];
        (count($pj->panitia)>0)?$data['panitia'] = $pj->panitia[0] :$data['panitia']=null;
        (count($pj->pp)>0)?$data['pp'] = $pj->pp[0] :$data['pp']=null;
        return response()->json([
                    'success' => true,
                    'data' => $data,
                        ], 201);
    }
    
    //update paket
    public function update_paketPPK($pkt_id,Request $req){
        $pkt = Paket::findOrFail($pkt_id);
        $dokumen = $this->dok->dokpkt($pkt_id);
        $data = [];
        if (!empty($req->paket)) {
            $pkt->update($req->paket);
            $data['paket'] = $pkt;
        }        
        if (!empty($req->lokasi)) {
            $lokasi = [];
            $this->delete_paketlokasi($pkt->pkt_id);
            foreach ($req->lokasi as $lok) {
                $lok['pkt_id'] = $pkt->pkt_id;
                array_push($lokasi, $this->create_paketlokasi($lok));
            }
            $data['lokasi']=$lokasi;
        }        
        if(!empty($req->dok_persiapan)){
            $data['dok_persiapan'] = $this->dok->updateDok($dokumen->dp_id, $req->dok_persiapan);
        }
        return response()->json(['success' => true,'data' => $data,], 201);
    }
    
    public function update_paket($pkt_id,Request $req){
        $paket = Paket::findOrFail($pkt_id);
        $data = $req->all();
        $paket->update($data);
        if($paket) {
            return response()->json([
                'success' => true,
                'data'    => $paket,  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    
    //anggaran
    public function tambah_ang($pkt_id, Request $req) {
        $paket = Paket::findOrFail($pkt_id);
        $anggaran = Anggaran::findOrFail($req->ang_id);
        if ($this->create_paketangg(['ang_id' => $anggaran->ang_id, 'pkt_id' => $paket->pkt_id])) {
            $paket->update(['pkt_pagu' => $paket->pkt_pagu + $anggaran->ang_nilai, 'pkt_nama' => $paket->pkt_nama . ',' . $anggaran->ang_nama]);
            $lokasi = ['pkt_id' => $paket->pkt_id, 'prop' => $anggaran->lokasi['propinsi'], 'kota' => $anggaran->lokasi['kota'], 'pkt_lokasi' => $anggaran->lokasi['lokasi']];
            $this->create_paketlokasi($lokasi);
            return response()->json(['success' => true,'data' => ['pkt_id' => $paket->pkt_id]], 201);
        }
    }
    
    public function edit_ang($pkt_id,Request $req){
        $paket = Paket::findOrFail($pkt_id);
        $anggaran = Anggaran::findOrFail($req->ang_id);
        if($this->delete_paketangg($paket->pkt_id) && $this->delete_paketlokasi($paket->pkt_id)){
            $paket->update(['pkt_pagu'=>$anggaran->ang_nilai,'pkt_nama'=>$anggaran->ang_nama]);
            $this->create_paketangg(['ang_id'=>$anggaran->ang_id,'pkt_id'=>$paket->pkt_id]);
            $lokasi = ['pkt_id'=>$paket->pkt_id,'prop'=>$anggaran->lokasi['propinsi'],'kota'=>$anggaran->lokasi['kota'],'pkt_lokasi'=>$anggaran->lokasi['lokasi']];
            $this->create_paketlokasi($lokasi);
            return response()->json([
                'success' => true,
                'data'=>['pkt_id'=>$paket->pkt_id]
            ], 201);
        }
    }
    
    
    // paket utility     
    public function paket_pp($pkt_id,Request $req){
        return $this->create_paketpp($pkt_id, $req->peg_id);
//        return $this->paket_penanggung($pkt_id,'pp', $req->peg_id);
    }
    
    public function paket_panitia($pkt_id,Request $req){
        return $this->create_paketpanitia($pkt_id, $req->pnt_id);
//        return $this->paket_penanggung($pkt_id,'panitia', $req->pnt_id);
    }
    
    public function create_paket($data){
        $data['pkt_tgl_buat'] = \Carbon\Carbon::now()->format('Y-m-d');
        return Paket::Create($data);
    } 
    
    protected function create_paketppk($pkt_id){
        return DB::table('paket_pp')->updateOrInsert(['pkt_id'=>$pkt_id],['ppk_id'=>$this->user['user_id'],'pkt_id'=>$pkt_id]);
    }
    
    protected function create_paketpp($pkt_id,$peg_id){
        return DB::table('paket_pp')->updateOrInsert(['pkt_id'=>$pkt_id],['pp_id'=>$peg_id,'pkt_id'=>$pkt_id]);
    }
    
    protected function create_paketpanitia($pkt_id,$pnt_id){
         return DB::table('paket_panitia')->updateOrInsert(['pkt_id'=>$pkt_id],['pnt_id'=>$pnt_id,'pkt_id'=>$pkt_id]);
    }
    
    
    public function create_paketangg($data){
        return DB::table('paket_anggaran')->updateOrInsert($data);
    }
    
    
    public function delete_paketlokasi($pkt_id){
        return PktLokasi::where('pkt_id',$pkt_id)->delete();
    }
    
    public function delete_paketangg($pkt_id){
        return DB::table('paket_anggaran')->where('pkt_id',$pkt_id)->delete();
    }

    public function create_paketlokasi($data){
        return PktLokasi::create($data);
    }
    
    public function get_paketlokasi($pkt_id){
        return DB::table('paket_lokasi')->where('pkt_id',$pkt_id)->get();
    }
    
    public function list_paket(Request $req){
        $paket = new Paket();
//        $paket = Paket::pegawai(23548499);
        $l =10;
        $path = URL::current();
        $total = $paket->count();
        if($req->length){
            $l = $req->length;
            $path.='?length='.$l;
        }
        $list = $paket->paginate($l);
        $list->withPath(url($path));
        return response()->json([
            'success'=>true,
            'total'=>$total,
            'data'=>$list
        ],201);
    }
    
    public function list_paketPPK(Request $req,$user_id){
        $pkt = Paket::where('ppk_id',$user_id);
        return $this->paginate($req, $pkt);
    }
    
    public function list_paketKIPBJ(Request $req,$user_id){
        $pkt = Paket::where('kipbj_id',$user_id)->orWhere('pkt_status',0);
        return $this->paginate($req, $pkt);
    }
    
    
    protected function paginate($req,$pkt){
        $l = 10;
        $path = URL::current();
        $total = $pkt->count();
        if ($req->length) {
            $l = $req->length;
            $path .= '?length=' . $l;
        }
        if($req->q){
            $q = $req->q;
            $list = $pkt->orWhere('paket.pkt_nama','LIKE',"%{$q}%");
            ($req->length) ? $path.='&q='.$q : $path.='?q='.$q ;
        }
        $pkt->orderBy('pkt_tgl_buat', 'desc');
        $list = $pkt->paginate($l);
        $list->withPath(url($path));
        return response()->json(['success' => true,'total' => $total,'data' => $list], 201);
    }


    private function generateID($table, $key) {
        do {
            $id = random_int("1" . str_repeat("0", 7), str_repeat("9", 8));
        } while (DB::table($table)->where($key, $id)->exists());
        return $id;
    }
    
    //    protected function paket_penanggung($pkt_id,$mode,$data){
//        $paket = Paket::findOrFail($pkt_id);
//        switch($mode){
//            case 'panitia' :
//                $paket->update(['pnt_id'=>$data]);
//                return response()->json([
//                'success' => true
//                ], 201);
//            case 'pp' :
//                $paket->update(['pp_id'=>$data]);
//                return response()->json([
//                'success' => true
//                ], 201);
//            default :
//                return response()->json([
//                'success' => false
//                ], 401);
//        }
//    }
}
