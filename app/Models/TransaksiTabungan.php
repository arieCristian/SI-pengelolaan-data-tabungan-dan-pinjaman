<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiTabungan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'transaksi_tabungan';

    public function tabungan(){
        return $this->belongsTo(Tabungan::class, 'tabungan_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'users_id');
    }

    public function bunga_reguler(){
        return $this->belongsTo(BungaReguler::class, 'bunga_id');
    }
}
