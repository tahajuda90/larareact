<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\RandomIntId;

class RknLndsnHukum extends Model
{
    use RandomIntId;
//    use HasFactory;
    
    protected $table = 'landasan_hukum_rekanan';
    protected $primaryKey = 'lhkp_id';
    public $incrementing = false;
    protected $guarded = ['lhkp_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 8;
    }
}
