<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class Panitia extends Model
{
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'panitia';
    protected $primaryKey = 'pnt_id';
    public $incrementing = false;
    protected $guarded = ['pnt_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 8;
    }
    
    public function pegawai(){
//        return $this->hasMany(AnggotaPnt::class,'pnt_id','pnt_id');
//        return $this->hasManyThrough(AnggotaPnt::class, Pegawai::class, 'peg_id', 'pnt_id', 'pnt_id', 'peg_id');
        return $this->belongsToMany(Pegawai::class, 'anggota_panitia', 'pnt_id', 'peg_id');
    }
    
    public function anggota(){
        return $this->hasMany(AnggotaPnt::class,'pnt_id','pnt_id');
    }
    
    public function paket(){
        return $this->belongsToMany(Paket::class,'paket_panitia','pnt_id','pkt_id');
    }
}
