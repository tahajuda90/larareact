<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class DokPersiapan extends Model
{
//    use HasFactory;
    use RandomIntId;
    
    protected $table = 'dok_persiapan';
    protected $primaryKey = 'dp_id';
    public $incrementing = false;
    protected $guarded = ['dp_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected $casts = [
        'dp_dkh' => 'array',
    ];

    protected function getIdLength(){
        return 7;
    }
}
