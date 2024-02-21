<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class BeritaAcara extends Model
{
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'berita_acara_nonlelang';
    protected $primaryKey = 'brc_id';
    public $incrementing = false;
    protected $guarded = ['brc_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 7;
    }
}
