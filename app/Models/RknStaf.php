<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class RknStaf extends Model
{
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'staf_ahli_rekanan';
    protected $primaryKey = 'stp_id';
    public $incrementing = false;
    protected $guarded = ['stp_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 8;
    }
}
