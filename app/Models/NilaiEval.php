<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class NilaiEval extends Model
{
    
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'nilai_evaluasi';
    protected $primaryKey = 'nev_id';
    public $incrementing = false;
    protected $guarded = ['nev_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 7;
    }
    
    protected $casts = [
        'nev_dkh' => 'array',
    ];
    
    public function eval(){
        return $this->belongsTo(Evaluasi::class, 'eva_id', 'eva_id');
    }
}
