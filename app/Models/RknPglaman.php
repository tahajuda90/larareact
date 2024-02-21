<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class RknPglaman extends Model
{
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'pengalaman_rekanan';
    protected $primaryKey = 'pen_id';
    public $incrementing = false;
    protected $guarded = ['pen_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 8;
    }
}
