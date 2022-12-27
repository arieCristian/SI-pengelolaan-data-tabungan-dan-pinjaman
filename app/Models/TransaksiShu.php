<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiShu extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'transaksi_shu';


    public function nasabah(){
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }
    public function shu(){
        return $this->belongsTo(Tabungan::class, 'shu_id');
    }
}
