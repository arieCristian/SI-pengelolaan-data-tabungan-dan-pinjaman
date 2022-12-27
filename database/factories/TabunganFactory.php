<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tabungan>
 */
class TabunganFactory extends Factory
{
    private static $id = 1;
    private static $no = 1;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nasabah_id' => self::$id++ ,
            'no' => self::$no++,
            'users_id' => $this->faker->randomElement([24,25]),
            'jenis' => 'reguler',
            'bunga' => 0.002,
            'total' =>  $this->faker->randomElement([100000]),
            'status' => 'masih berjalan',
            'created_at'=> '07-08-2022',
        ];
    }
}
