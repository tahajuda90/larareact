<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function upload(Request $req){
        $filePath = $req->file('file');
        $fileName = md5(time().$filePath->getClientOriginalName());
        $path = $filePath->storeAs('file',$fileName,'public');
        
        $konten = New Content();
        $konten->ctn_id_content = ($req->id_content) ? $req->id_content : $konten->generateRandId1('ctn_id_content',8);
        $konten->ctn_versi = $konten->generateRandId1('ctn_versi',6);
        $konten->audituser = 'admin';
        $konten->blb_date_time = \Carbon\Carbon::now();
        $konten->blb_path = json_encode(array('name'=>$filePath->getClientOriginalName(),'ext'=>$filePath->getClientOriginalExtension(),'savename'=>$fileName,'size'=>$filePath->getSize(),'filepath'=>$path));
        $konten->save();
        
        return response()->json($konten,201);
    }
    
    public function download(Request $req){
        $id_content = $req->id_content;
        $versi = $req->versi;
        $content = json_decode(Content::where('ctn_id_content',$id_content)->where('ctn_versi',$versi)->first()->blb_path);
//        return response()->download(storage_path().'/app/public/file/'.json_decode($content)->savename,json_decode($content)->name);
//        return $content;
//        return response()->download(storage_path('/app/public/file/'.$content->savename),$content->name);
        return Storage::disk('public')->download('file/'.$content->savename,$content->name);
    }
    
    public function delete(Request $req){
        $id_content = $req->id_content;
        $versi = $req->versi;
        $konten = Content::where('ctn_id_content',$id_content)->where('ctn_versi',$versi)->first();
        $content = json_decode($konten->blb_path);
//        print_r($content);
        if(Storage::disk('public')->exists('file/'.$content->savename)){
            Storage::disk('public')->delete('file/'.$content->savename);
            Content::where('ctn_id_content',$id_content)->where('ctn_versi',$versi)->delete();
            return response()->json('file berhasil dihapus',201);
        }
        return response()->json('file not found',409);
    }
    
    public function list(Request $req){
        $id_content = $req->id_content;
        $konten = Content::where('ctn_id_content',$id_content)->get();
        if($konten){
            return response()->json($konten,201);
        }
        return response()->json('tidak ditemukan',409);
    }
}
