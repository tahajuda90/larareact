<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class PktLokasi extends Model
{
    use RandomIntId;
//    use HasFactory;
    protected $table = 'paket_lokasi';
    protected $primaryKey = 'pkl_id';
    public $incrementing = false;
    protected $guarded = ['pkl_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 7;
    }
}
