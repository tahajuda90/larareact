<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggaran;
use App\Http\Controllers\Api\IntegrasiSirup;
use App\Http\Controllers\Api\AuthController;

class AnggaranController extends Controller
{
    
    protected $rup;
    protected $user = null;
    
    public function __construct() {
        $auth = new AuthController();
        $this->user = $auth->user();
        $this->rup = New IntegrasiSirup();
    }
    
    public function add_anggaran(Request $req){
        $kode = $req->rup;
        if (!empty($this->rup->rup_integrate($kode))) {
            $rupdta = (object) $this->rup->rup_integrate($kode);
            $ins = ['rup_id' => $rupdta->rup_id, 'sbd_id' => $rupdta->sbd_id, 'ang_koderekening' => $rupdta->angg_rekening,
                'ang_nilai' => $rupdta->angg_nilai, 'ang_uraian' => $rupdta->uraian, 'ang_tahun' => $rupdta->angg_thn, 'ang_nama' => $rupdta->pkt_nama,'lokasi'=>$rupdta->lokasi,'kgr_id'=>$rupdta->jns_pengadaan];
            (isset($this->user))?$ins['audituser']= $this->user['user']:'';
            $anggaran = $this->create($ins);
            $data = ['ang_id' => $anggaran->ang_id, 'rup' => $rupdta];
            return response()->json([
                        'success' => true,
                        'data' => $data,
                            ], 201);
            
        }
         return response()->json([
                    'success' => false
                        ], 404);
    }
    
    
    public function create($data){
        return Anggaran::updateOrCreate(['rup_id'=>$data['rup_id']],$data);
    }
    
    public function anggaranpkt($pkt_id){
        return Anggaran::join('paket_anggaran','anggaran.ang_id','=','paket_anggaran.ang_id')->where('paket_anggaran.pkt_id',$pkt_id)->get(['paket_anggaran.pkt_id','anggaran.*']);
    }
}
