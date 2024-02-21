<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\RandomIntId;

class Rekanan extends Model
{
        use RandomIntId;
    // use HasFactory;
    protected $table = 'rekanan';
    protected $primaryKey = 'rkn_id';
    public $incrementing = false;
    protected $guarded = ['rkn_id','auditupdate'];

    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected $hidden = [
        'passw',
        'token',
        'auditupdate',
        'audituser'
    ];

    protected function getIdLength(){
        return 8;
    }
    
    public function b_usaha(){
        return $this->belongsTo(B_Usaha::class,'btu_id','btu_id');
    }
}
