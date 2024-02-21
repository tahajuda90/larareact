<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class DokNonLel extends Model
{
//    use HasFactory;
    
    use RandomIntId;
    
    protected $table = 'dok_nonlelang';
    protected $primaryKey = 'dll_id';
    public $incrementing = false;
    protected $guarded = ['dll_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected $casts = [
        'dll_id' => 'array',
    ];

    public function NonLel(){
        return $this->belongsTo(NonLelSeleksi::class, 'lls_id','lls_id');
    }
    
    public function content(){
        return $this->hasMany(DokNonLelContent::class, 'dll_id', 'dll_id');
    }
    
    protected function getIdLength(){
        return 7;
    }
    
}
