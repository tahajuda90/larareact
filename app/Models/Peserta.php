<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class Peserta extends Model
{
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'peserta_nonlelang';
    protected $primaryKey = 'psr_id';
    public $incrementing = false;
    protected $guarded = ['psr_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected $casts = [
        'psr_dkh' => 'array',
    ];
    
    protected function getIdLength(){
        return 8;
    }
}
