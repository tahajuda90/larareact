<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokNonLelContent extends Model
{
//    use HasFactory;
    
    protected $table = 'dok_nonlelang_content';
    protected $primaryKey = null;
    public $incrementing = false;
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    protected $guarded = ['auditupdate'];
    
    protected $casts = [
        'dll_dkh' => 'array',
    ];
    
    public function doknonlel(){
        return $this->belongsTo(DokNonLel::class,'dll_id','dll_id');
    }
}
