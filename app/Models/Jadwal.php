<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class Jadwal extends Model
{
    use RandomIntId;
//    use HasFactory;
    
    
    protected $table = 'jadwal';
    protected $primaryKey = 'dtj_id';
    public $incrementing = false;
    protected $guarded = ['dtj_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 8;
    }
}
