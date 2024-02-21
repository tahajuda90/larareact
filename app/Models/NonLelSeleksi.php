<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use \App\RandomIntId;

class NonLelSeleksi extends Model
{
    use RandomIntId;
//    use HasFactory;
    protected $table = 'nonlelang_seleksi';
    protected $primaryKey = 'lls_id';
    public $incrementing = false;
    protected $guarded = ['lls_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 8;
    }
    
    public function paket(){
        return $this->belongsTo(Paket::class, 'pkt_id', 'pkt_id');
    }
    
    public function DokNonLel(){
        return $this->hasMany(DokNonLel::class, 'lls_id', 'lls_id');
    }
    
    public function Content(){
        return $this->hasManyThrough(DokNonLelContent::class, DokNonLel::class, 'lls_id', 'dll_id', 'lls_id');
    }
    
    public function persetujuan(){
        return $this->hasMany(Persetujuan::class, 'lls_id','lls_id'); 
    }
    
    public function scopePP(Builder $query,$user_id){
        return $query->join('paket_pp', 'paket_pp.pkt_id', '=', 'nonlelang_seleksi.pkt_id', 'left')
                ->where('paket_pp.pp_id',$user_id);
    }
    
    public function scopePanitia(Builder $query,$user_id){
         return $query->join('paket_panitia','paket_panitia.pkt_id','=','nonlelang_seleksi.pkt_id','left')
                ->join('anggota_panitia','anggota_panitia.pnt_id','=','paket_panitia.pnt_id','left')
                ->where('anggota_panitia.peg_id',$user_id);
    }
    
    public function scopePeserta(Builder $query,$user_id){
        return $query->join('peserta_nonlelang','peserta_nonlelang.lls_id','=','nonlelang_seleksi.lls_id','left')->where('peserta_nonlelang.rkn_id',$user_id);
    }
    
    public function scopePPK(Builder $query,$user_id){
        return $query->join('paket','paket.pkt_id','=','nonlelang_seleksi.pkt_id','left')->where('paket.ppk_id',$user_id);
    }
    
    public function scopeKIPBJ(Builder $query,$user_id){
        return $query->join('paket','paket.pkt_id','=','nonlelang_seleksi.pkt_id','left')->where('paket.kipbj_id',$user_id);
    }
}
