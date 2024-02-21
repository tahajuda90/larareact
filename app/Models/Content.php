<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    
    
    protected $table = 'content';
    protected $primaryKey = null;
    public $incrementing = false;
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
//    use HasFactory;
    
    protected $casts = [
    'blb_path' => 'encrypted',
    ];
    
    
    public function generateRandId1($key,$lentgh) {
        do {
            $id = random_int("1" . str_repeat("0", $lentgh-1), str_repeat("9", $lentgh));
        } while (parent::where($key, $id)->exists());
        return $id;
    }
    
    
    
}
