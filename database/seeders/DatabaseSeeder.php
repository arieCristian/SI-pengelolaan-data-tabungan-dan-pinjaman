<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Nasabah;
use App\Models\Pinjaman;
use App\Models\Tabungan;
use Illuminate\Database\Seeder;
use App\Models\TransaksiPinjaman;
use App\Models\TransaksiTabungan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(20)->create();
        \App\Models\Nasabah::factory(20)->create();

        User::create([
            'nama' => 'Arie Cristian',
            'username' => 'admin',
            'no_telp' => '085234445627',
            'role' => 'admin',
            'password' => bcrypt('admin')
        ]);
        User::create([
            'nama' => 'Muhamad Ibrahim',
            'username' => 'administrasi',
            'no_telp' => '0823567892972',
            'role' => 'administrasi',
            'password' => bcrypt('administrasi')
        ]);
        User::create([
            'nama' => 'Cristian Ronaldo',
            'username' => 'kasir',
            'no_telp' => '088773668291',
            'role' => 'kasir',
            'password' => bcrypt('kasir')
        ]);
        User::create([
            'nama' => 'Sri Ayu Widianti',
            'username' => 'kolektor1',
            'no_telp' => '087823411242',
            'role' => 'kolektor',
            'password' => bcrypt('kolektor')
        ]);
        User::create([
            'nama' => 'Ayu Ulandari',
            'username' => 'kolektor2',
            'no_telp' => '087654345281',
            'role' => 'kolektor',
            'password' => bcrypt('kolektor')
        ]);
        
       

        



    }
}
