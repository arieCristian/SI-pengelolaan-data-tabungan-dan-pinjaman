<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BungaReguler extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'bunga_reguler';

    public function transaksi_tabungan(){
        return $this->hasMany(TransaksiTabungan::class);
    }


}
