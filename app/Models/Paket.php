<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use \App\RandomIntId;

class Paket extends Model
{
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'paket';
    protected $primaryKey = 'pkt_id';
    public $incrementing = false;
    protected $guarded = ['pkt_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 8;
    }
    
    public function panitia(){
        return $this->belongsToMany(Panitia::class, 'paket_panitia', 'pkt_id', 'pnt_id');
    }
    
    public function pp(){
        return $this->belongsToMany(Pegawai::class, 'paket_pp', 'pkt_id', 'pp_id', 'pkt_id', 'peg_id');
    }
    
    public function nonLelang(){
        return $this->hasMany(NonLelSeleksi::class, 'pkt_id', 'pkt_id');
    }
    
    public function scopePP(Builder $query,$user_id){
        return $query->join('paket_pp', 'paket_pp.pkt_id', '=', 'paket.pkt_id', 'left')->where('paket_pp.pp_id',$user_id);
    }
}
