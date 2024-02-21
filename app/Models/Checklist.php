<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class Checklist extends Model
{
    
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'checklist';
    protected $primaryKey = 'chk_id';
    public $incrementing = false;
    protected $guarded = ['chk_id'];
    public $timestamps = false;
    
//    const CREATED_AT = 'auditupdate';
//    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 8;
    }
}
