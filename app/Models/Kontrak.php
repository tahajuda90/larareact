<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class Kontrak extends Model
{
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'kontrak';
    protected $primaryKey = 'kontrak_id';
    public $incrementing = false;
    protected $guarded = ['kontrak_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 7;
    }
}
