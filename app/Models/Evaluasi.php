<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class Evaluasi extends Model
{
    
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'evaluasi';
    protected $primaryKey = 'eva_id';
    public $incrementing = false;
    protected $guarded = ['eva_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected $casts = [
        'nev_dkh' => 'array',
    ];
    
    protected function getIdLength(){
        return 7;
    }
    
    public function nilai(){
        return $this->hasMany(NilaiEval::class, 'eva_id', 'eva_id');
    }
}
