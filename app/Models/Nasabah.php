<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'nasabah';


    public function user(){
        return $this->belongsTo(User::class, 'users_id');
    }
    public function kolektor(){
        return $this->belongsTo(User::class, 'kolektor');
    }
    public function pinjaman(){
        return $this->hasMany(Pinjaman::class);
    }
    public function tabungan(){
        return $this->hasMany(Tabungan::class);
    }
    public function transaks_shu(){
        return $this->hasMany(TransaksiShu::class);
    }
}
