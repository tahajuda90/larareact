<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class PegawaiAuth extends Authenticatable implements JWTSubject
{
    // use HasFactory;
    use HasApiTokens, Notifiable;
    protected $table = 'pegawai';
    protected $primaryKey = 'peg_id';


    protected $fillable = [
        'peg_namauser',
        'passw',
    ];

    protected $hidden = [
        'passw',
        'token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthPassword() {
        return $this->passw;
    }

    public function getRememberToken(){
        return $this->token;
    }
}
