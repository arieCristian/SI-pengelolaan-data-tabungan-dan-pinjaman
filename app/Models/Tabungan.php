<?php

namespace App\Models;

use App\Http\Controllers\TransaksiTabunganController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabungan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'tabungan';

    public function nasabah(){
        return $this->belongsTo(Nasabah::class, 'nasabah_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'users_id');
    }
    public function transaksi_tabungan(){
        return $this->hasMany(TransaksiTabungan::class);
    }
}
