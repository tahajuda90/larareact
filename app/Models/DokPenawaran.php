<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class DokPenawaran extends Model
{
//    use HasFactory;
     use RandomIntId;
    
    protected $table = 'dok_penawaran';
    protected $primaryKey = 'dok_id';
    public $incrementing = false;
    protected $guarded = ['dok_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 7;
    }
    
}
