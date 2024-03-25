<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\RandomIntId;

class Spk extends Model
{
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'spk';
    protected $primaryKey = 'spk_id';
    public $incrementing = false;
    protected $guarded = ['spk_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected $casts = [
        'spk_content' => 'array',
    ];
    
    protected function getIdLength(){
        return 7;
    }
}
