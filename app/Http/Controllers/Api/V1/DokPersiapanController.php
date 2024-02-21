<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DokPersiapan;

class DokPersiapanController extends Controller
{
    //
    
    public function update_dokpersiapan($dp_id,Request $req){
        $dokumen = $this->updateDok($dp_id, $req->all());
        if($dokumen) {
            return response()->json([
                'success' => true,
                'data'    => $dokumen  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
    
    public function createDok($data){
        return DokPersiapan::create($data);
    }
    
    public function updateDok($dp_id,$data){
        $dok = DokPersiapan::findOrFail($dp_id);
        $dok->update($data);
        return $dok;
    }
    
    public function dokpkt($pkt_id){
        return DokPersiapan::where('pkt_id',$pkt_id)->orderByDesc('dp_versi')->first();
    }
}
