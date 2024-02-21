<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class Persetujuan extends Model
{
    use RandomIntId;
//    use HasFactory;
    protected $table = 'persetujuan';
    protected $primaryKey = 'pst_id';
    public $incrementing = false;
    protected $guarded = ['pst_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 7;
    }
    
    public function pegawai(){
        return $this->belongsTo(Pegawai::class,'peg_id','peg_id');
    }
    
    public function lelang(){
        return $this->belongsTo(NonLelSeleksi::class, 'lls_id','lls_id');
    }
}
