<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'pinjaman';


    public function nasabah(){
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }
    public function transaksi_pinjaman(){
        return $this->hasMany(TransaksiPinjaman::class);
    }
}
