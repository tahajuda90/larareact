<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\RandomIntId;

class RknIjinUsaha extends Model
{
    use RandomIntId;
//    use HasFactory;
    protected $table = 'ijin_usaha_rekanan';
    protected $primaryKey = 'ius_id';
    public $incrementing = false;
    protected $guarded = ['ius_id','auditupdate'];
    
    const CREATED_AT = 'auditupdate';
    const UPDATED_AT = 'auditupdate';
    
    protected function getIdLength(){
        return 8;
    }
    
    public function file(){
        return $this->hasMany(Content::class, 'ctn_id_content', 'ius_id_attachment');
    }
    
    public function scopeLelang(Builder $query,$lls_id){
        return $query->join('verif_ijin_usaha', 'verif_ijin_usaha.ius_id', '=', $this->table.'.'.$this->primaryKey, 'left')
                ->where('verif_ijin_usaha.lls_id',$lls_id);
    }
}
