<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IntegrasiSirup extends Controller
{
    public function sirup(Request $req){
        $rup = $req->rup;        
//        libxml_use_internal_errors(false);
//        dd($dta);
        if(!empty($this->rup_integrate($rup))){
            $dta = (object)$this->rup_integrate($rup);
            return response()->json([
                    'success' => true,
                    'data' => $dta,
                        ], 201);
        }
        return response()->json([
            'success' => false
        ],404);
    }
    
    public function rup_integrate($rup){
        $dta = null;
          $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Linux; Android 12; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.58 Mobile Safari/537.36'
            ])->get('https://sirup.lkpp.go.id/sirup/rup/detailPaketPenyedia2020', ['idPaket' => $rup ]);
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        if($response->getStatusCode() == 200){
        $doc->loadHTML((string) $response->getBody()->getContents());
        $cells = $doc->getElementsByTagName('td');
        $jnis = [1=>"Barang",2=>"Pekerjaan Konstruksi",3=>"Jasa Konsultansi",4=>"Jasa Lainnya"];        
            $dta = ['rup_id'=>$cells[17]->nodeValue,'pkt_nama'=>$cells[19]->nodeValue,
            'angg_thn'=>$cells[25]->nodeValue,'uraian'=>$cells[35]->nodeValue,'angg_nilai'=>$cells[66]->nodeValue
            ,'angg_rekening'=>$cells[58]->nodeValue,'sbd_id'=>$cells[55]->nodeValue,'jns_pengadaan'=> array_keys($jnis,$cells[63]->nodeValue)[0],
            'mtd_pengadaan'=>$cells[68]->nodeValue
            ,'lokasi'=>['propinsi'=> strtoupper($cells[29]->nodeValue),'kota'=>'KOTA KEDIRI','lokasi'=>$cells[31]->nodeValue]];

        libxml_use_internal_errors(false);
        return $dta;
        }
        return $dta;
    }
}
