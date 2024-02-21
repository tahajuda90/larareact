<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaPnt extends Model
{
//    use HasFactory;
     protected $table = 'anggota_panitia';
    protected $primaryKey = null;
    public $incrementing = false;
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    protected $guarded = ['auditupdate'];
    
    public function panitia(){
        return $this->belongsTo(Panitia::class,'pnt_id','pnt_id');
    }
    
    public function pegawai(){
        return $this->belongsTo(Pegawai::class,'peg_id','peg_id');
    }
}
