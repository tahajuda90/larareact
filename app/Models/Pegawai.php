<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\RandomIntId;

class Pegawai extends Model
{
    use RandomIntId;
    // use HasFactory;
    protected $table = 'pegawai';
    protected $primaryKey = 'peg_id';
    public $incrementing = false;
    protected $guarded = ['peg_id','auditupdate'];

    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected $hidden = [
        'passw',
        'token',
        'auditupdate',
        'audituser'
    ];

    protected function getIdLength(){
        return 8;
    }
    
    public function persetujuan(){
        return $this->hasMany(Persetujuan::class, 'peeg_id','peg_id');
    }
    
     public function AnggotaPanitia(){
        return $this->hasMany(AnggotaPnt::class,'peg_id','peg_id');
    }
    
}
