<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class Sppbj extends Model
{
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'sppbj';
    protected $primaryKey = 'sppbj_id';
    public $incrementing = false;
    protected $guarded = ['sppbj_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 7;
    }
}
