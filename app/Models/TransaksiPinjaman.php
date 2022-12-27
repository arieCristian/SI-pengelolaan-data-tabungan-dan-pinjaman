<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPinjaman extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'transaksi_pinjaman';

    public function pinjaman(){
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
    }
}
