<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransaksiTabungan>
 */
class TransaksiTabunganFactory extends Factory
{
    private static $id = 1;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'tabungan_id' => self::$id++ ,
            'users_id' => 23,
            'jenis' => 'pemindahan',
            'jumlah' => 100000,
            'tabungan_awal' => 0,
            'tabungan_akhir' => 100000,
            'created_at'=> '07-08-2022',
            'setor' => 'sudah'
        ];
    }
}
