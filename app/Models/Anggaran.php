<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class Anggaran extends Model
{
//    use HasFactory;
    use RandomIntId;
    
    protected $table = 'anggaran';
    protected $primaryKey = 'ang_id';
    public $incrementing = false;
    protected $guarded = ['ang_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected $casts = [
    'lokasi' => 'array',
    ];
    
    protected function getIdLength(){
        return 8;
    }
}
