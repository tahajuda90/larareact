<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B_Usaha extends Model
{
//    use HasFactory;
    protected $table = 'bentuk_usaha';
    protected $primaryKey = 'btu_id';
    public $incrementing = false;
    protected $keyType = 'char';
    
    public function rekanan(){
        return $this->hasMany(Rekanan::class,'btu_id','btu_id');
    }
}
