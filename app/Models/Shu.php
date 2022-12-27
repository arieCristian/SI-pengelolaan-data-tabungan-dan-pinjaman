<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shu extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'shu';

    public function transaks_shu(){
        return $this->hasMany(TransaksiShu::class);
    }
}
